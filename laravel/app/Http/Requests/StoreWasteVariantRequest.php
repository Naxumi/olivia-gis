<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Waste; // 👈 Impor model Waste
use Illuminate\Support\Facades\Auth; // 👈 Impor Auth
use Illuminate\Support\Facades\Log; // 👈 Import Log facade
use Illuminate\Support\Facades\DB; // 👈 Import DB facade (since you use DB::table)

class StoreWasteVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\Waste $parentWaste */
        $parentWaste = $this->route('waste'); // Mengambil objek Waste dari rute (misal dari /wastes/{waste}/variants)

        // Otorisasi: User harus pemilik dari toko (store) yang memiliki waste ini
        // atau seorang admin.
        // Asumsi: model Waste memiliki relasi 'store' dan model Store memiliki 'user_id'
        if ($parentWaste && $parentWaste->store) {
            return Auth::id() === $parentWaste->store->user_id || Auth::user()->roles()->where('name', 'admin')->exists();
        }
        return false; // Jika parentWaste atau store tidak ditemukan, tolak request.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'volume_in_grams' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Stok untuk varian baru ini
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function after(): array
    {
        // return [
        //     // function (\Illuminate\Validation\Validator $validator) {
        //     //     /** @var \App\Models\Waste $parentWaste */
        //     //     $parentWaste = $this->route('waste');

        //     //     // Jika $parentWaste tidak ditemukan atau validasi dasar gagal, jangan lanjutkan.
        //     //     if (!$parentWaste || $validator->fails()) {
        //     //         return;
        //     //     }

        //     //     $newVariantStock = (int) $this->input('stock');

        //     //     // Hitung total stok dari varian yang sudah ada untuk parentWaste ini
        //     //     $currentTotalExistingVariantsStock = $parentWaste->wasteVariants()->sum('stock');

        //     //     // Hitung total stok varian jika varian baru ini ditambahkan
        //     //     $proposedTotalVariantStock = $currentTotalExistingVariantsStock + $newVariantStock;

        //     //     if ($proposedTotalVariantStock > $parentWaste->stock) {
        //     //         $availableStock = max(0, $parentWaste->stock - $currentTotalExistingVariantsStock);
        //     //         $validator->errors()->add(
        //     //             'stock',
        //     //             "Total stok semua varian akan menjadi {$proposedTotalVariantStock}, yang melebihi stok utama limbah ({$parentWaste->stock}). " .
        //     //                 "Stok utama yang tersisa untuk dialokasikan ke varian baru adalah {$availableStock}."
        //     //         );
        //     //     }
        //     // }

        //     // function (\Illuminate\Validation\Validator $validator) {
        //     //     // /** @var \App\Models\Waste $parentWaste */
        //     //     // $parentWaste = $this->route('waste');

        //     //     // // Jika $parentWaste tidak ditemukan atau validasi dasar gagal, jangan lanjutkan.
        //     //     // if (!$parentWaste || $validator->fails()) {
        //     //     //     Log::warning('StoreWasteVariantRequest: Validasi after() dilewati.', [
        //     //     //         'parent_waste_id' => $parentWaste ? $parentWaste->id : null,
        //     //     //         'validator_fails' => $validator->fails()
        //     //     //     ]);
        //     //     //     return;
        //     //     // }

        //     //     // $newVariantStock = (int) $this->input('stock');
        //     //     // $currentTotalExistingVariantsStock = 0; // Default

        //     //     // Log::info('StoreWasteVariantRequest: Memulai validasi stok kustom.', [
        //     //     //     'parent_waste_id' => $parentWaste->id,
        //     //     //     'parent_stock_limit' => $parentWaste->stock,
        //     //     //     'new_variant_stock_input' => $newVariantStock
        //     //     // ]);

        //     //     // try {
        //     //     //     // --- OPSI PERHITUNGAN STOK VARIAN YANG ADA ---
        //     //     //     // Coba Opsi 2 terlebih dahulu jika Opsi 1 (yang Anda gunakan sebelumnya) boros memori.

        //     //     //     // Opsi 1: Menggunakan relasi Eloquent (yang mungkin jadi masalah)
        //     //     //     // $currentTotalExistingVariantsStock = (int) $parentWaste->wasteVariants()->sum('stock');

        //     //     //     // Opsi 2: Menggunakan Query Builder langsung (lebih ringan, minim overhead Eloquent)
        //     //     //     $currentTotalExistingVariantsStock = (int) DB::table('waste_variants')
        //     //     //         ->where('waste_id', $parentWaste->id)
        //     //     //         ->sum('stock');

        //     //     //     Log::info('StoreWasteVariantRequest: Hasil perhitungan stok varian yang ada.', [
        //     //     //         'waste_id' => $parentWaste->id,
        //     //     //         'sum_existing_variants_stock' => $currentTotalExistingVariantsStock
        //     //     //     ]);
        //     //     // } catch (\Exception $e) {
        //     //     //     Log::error('StoreWasteVariantRequest: Gagal menghitung total stok varian yang ada.', [
        //     //     //         'waste_id' => $parentWaste->id,
        //     //     //         'error' => $e->getMessage(),
        //     //     //         'trace' => $e->getTraceAsString() // Bisa sangat panjang, hati-hati di production
        //     //     //     ]);
        //     //     //     // Jika gagal menghitung, anggap validasi gagal untuk keamanan
        //     //     //     $validator->errors()->add('stock', 'Terjadi kesalahan saat memvalidasi stok. Silakan coba lagi.');
        //     //     //     return;
        //     //     // }

        //     //     // // Hitung total stok varian jika varian baru ini ditambahkan
        //     //     // $proposedTotalVariantStock = $currentTotalExistingVariantsStock + $newVariantStock;

        //     //     // if ($proposedTotalVariantStock > $parentWaste->stock) {
        //     //     //     $availableStock = max(0, $parentWaste->stock - $currentTotalExistingVariantsStock);
        //     //     //     $validator->errors()->add(
        //     //     //         'stock',
        //     //     //         "Total stok semua varian akan menjadi {$proposedTotalVariantStock}, yang melebihi stok utama limbah ({$parentWaste->stock}). " .
        //     //     //             "Stok utama yang tersisa untuk dialokasikan ke varian baru adalah {$availableStock}."
        //     //     //     );
        //     //     //     Log::warning('StoreWasteVariantRequest: Validasi stok GAGAL.', [
        //     //     //         'parent_waste_id' => $parentWaste->id,
        //     //     //         'proposed_total' => $proposedTotalVariantStock,
        //     //     //         'parent_limit' => $parentWaste->stock
        //     //     //     ]);
        //     //     // } else {
        //     //     //     Log::info('StoreWasteVariantRequest: Validasi stok BERHASIL.');
        //     //     // }

        //     //     // SECOND TRY

        //     // }

        //     // function (\Illuminate\Validation\Validator $validator) {
        //     //     /** @var \App\Models\Waste $parentWaste */
        //     //     $parentWaste = $this->route('waste');

        //     //     if (!$parentWaste || $validator->fails()) {
        //     //         Log::warning('StoreWasteVariantRequest: Validasi after() dilewati (awal).', [
        //     //             'parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL', // Handle jika $parentWaste null
        //     //             'validator_fails' => $validator->fails()
        //     //         ]);
        //     //         return;
        //     //     }

        //     //     $newVariantStockInput = $this->input('stock');
        //     //     Log::info('StoreWasteVariantRequest: Input stock', ['new_variant_stock_input' => $newVariantStockInput, 'type' => gettype($newVariantStockInput)]);

        //     //     $parentStockLimit = null;
        //     //     try {
        //     //         $parentStockLimit = $parentWaste->stock;
        //     //         Log::info('StoreWasteVariantRequest: Parent stock limit', ['parent_stock_limit' => $parentStockLimit, 'type' => gettype($parentStockLimit)]);
        //     //     } catch (\Exception $e) {
        //     //         Log::error('StoreWasteVariantRequest: Error mengambil parentWaste->stock', ['error' => $e->getMessage()]);
        //     //         $validator->errors()->add('stock', 'Gagal mengambil data stok utama.');
        //     //         return;
        //     //     }

        //     //     $currentTotalExistingVariantsStock = null;
        //     //     try {
        //     //         $currentTotalExistingVariantsStock = DB::table('waste_variants')
        //     //             ->where('waste_id', $parentWaste->id)
        //     //             ->sum('stock');
        //     //         $currentTotalExistingVariantsStock = ($currentTotalExistingVariantsStock === null) ? 0 : (int) $currentTotalExistingVariantsStock; // Handle null sum, cast to int
        //     //         Log::info('StoreWasteVariantRequest: Sum existing variants stock', ['sum' => $currentTotalExistingVariantsStock, 'type' => gettype($currentTotalExistingVariantsStock)]);
        //     //     } catch (\Exception $e) {
        //     //         Log::error('StoreWasteVariantRequest: Error saat sum stock varian', ['error' => $e->getMessage()]);
        //     //         $validator->errors()->add('stock', 'Gagal menghitung total stok varian.');
        //     //         return;
        //     //     }

        //     //     $newVariantStock = (int) $newVariantStockInput; // Pastikan integer
        //     //     $proposedTotalVariantStock = null;
        //     //     try {
        //     //         $proposedTotalVariantStock = $currentTotalExistingVariantsStock + $newVariantStock;
        //     //         Log::info('StoreWasteVariantRequest: Proposed total stock', ['proposed' => $proposedTotalVariantStock, 'type' => gettype($proposedTotalVariantStock)]);
        //     //     } catch (\Exception $e) {
        //     //         Log::error('StoreWasteVariantRequest: Error saat kalkulasi proposedTotalVariantStock', ['error' => $e->getMessage()]);
        //     //         $validator->errors()->add('stock', 'Gagal kalkulasi total stok yang diajukan.');
        //     //         return;
        //     //     }

        //     //     if ($proposedTotalVariantStock > $parentStockLimit) {
        //     //         $availableStock = max(0, $parentStockLimit - $currentTotalExistingVariantsStock);
        //     //         $validator->errors()->add(
        //     //             'stock',
        //     //             "Total stok semua varian akan menjadi {$proposedTotalVariantStock}, yang melebihi stok utama limbah ({$parentStockLimit}). " .
        //     //                 "Stok utama yang tersisa untuk dialokasikan ke varian baru adalah {$availableStock}."
        //     //         );
        //     //         Log::warning('StoreWasteVariantRequest: Validasi stok GAGAL.', [
        //     //             'parent_waste_id' => $parentWaste->id,
        //     //             'proposed_total' => $proposedTotalVariantStock,
        //     //             'parent_limit'   => $parentStockLimit
        //     //         ]);
        //     //     } else {
        //     //         Log::info('StoreWasteVariantRequest: Validasi stok BERHASIL.');
        //     //     }
        //     // }

        // //     function (\Illuminate\Validation\Validator $validator) {
        // //     Log::info('StoreWasteVariantRequest: after() hook DIMULAI.'); // Log paling awal

        // //     /** @var \App\Models\Waste $parentWaste */
        // //     $parentWaste = $this->route('waste');
        // //     Log::info('StoreWasteVariantRequest: $parentWaste diambil dari route.', ['parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL']);

        // //     if (!$parentWaste || $validator->fails()) {
        // //         Log::warning('StoreWasteVariantRequest: Validasi after() dilewati (awal).', [
        // //             'parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL',
        // //             'validator_fails' => $validator->fails()
        // //         ]);
        // //         return;
        // //     }

        // //     $newVariantStock = (int) $this->input('stock');
        // //     Log::info('StoreWasteVariantRequest: Input stock.', ['new_variant_stock_input' => $newVariantStock]);

        // //     // SEKARANG, HANYA LOG, TIDAK ADA OPERASI DATABASE ATAU ARITMATIKA BERAT
        // //     Log::info('StoreWasteVariantRequest: Mencoba mengakses parentWaste->stock.', ['parent_stock_property_exists' => property_exists($parentWaste, 'stock')]);
        // //     if(property_exists($parentWaste, 'stock')) {
        // //         Log::info('StoreWasteVariantRequest: Nilai parentWaste->stock.', ['parent_stock_value' => $parentWaste->stock]);
        // //     }


        // //     // HENTIKAN EKSEKUSI DI SINI SEMENTARA UNTUK MELIHAT APAKAH LOG MUNCUL
        // //     // Anda bisa uncomment bagian bawah jika log di atas sudah muncul.
        // //     /*
        // //     $currentTotalExistingVariantsStock = 0; // Default
        // //     try {
        // //         $currentTotalExistingVariantsStock = (int) DB::table('waste_variants')
        // //                                                     ->where('waste_id', $parentWaste->id)
        // //                                                     ->sum('stock');
        // //         Log::info('StoreWasteVariantRequest: Sum existing variants stock', ['sum' => $currentTotalExistingVariantsStock]);
        // //     } catch (\Exception $e) {
        // //         Log::error('StoreWasteVariantRequest: Error saat sum stock varian', ['error' => $e->getMessage()]);
        // //         $validator->errors()->add('stock', 'Gagal menghitung total stok varian.');
        // //         return;
        // //     }

        // //     $proposedTotalVariantStock = $currentTotalExistingVariantsStock + $newVariantStock;
        // //     Log::info('StoreWasteVariantRequest: Proposed total stock', ['proposed' => $proposedTotalVariantStock]);

        // //     if ($proposedTotalVariantStock > $parentWaste->stock) {
        // //         // ... (logika error) ...
        // //     } else {
        // //         Log::info('StoreWasteVariantRequest: Validasi stok BERHASIL.');
        // //     }
        // //     */
        // //     Log::info('StoreWasteVariantRequest: after() hook SELESAI (sebelum validasi stok utama).');
        // // }
        // // Log ini akan muncul SETIAP KALI method after() dipanggil
        // Log::info('StoreWasteVariantRequest: Method after() DIPANGGIL.', ['time' => microtime(true)]);

        // return [
        //     function (\Illuminate\Validation\Validator $validator) {
        //         // Log ini akan muncul SETIAP KALI CLOSURE di dalam after() dieksekusi
        //         Log::info('StoreWasteVariantRequest: Closure di dalam after() DIMULAI.', ['time' => microtime(true)]);

        //         /** @var \App\Models\Waste $parentWaste */
        //         $parentWaste = $this->route('waste');
        //         Log::info('StoreWasteVariantRequest: $parentWaste diambil dari route.', ['parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL']);

        //         if (!$parentWaste || $validator->fails()) {
        //             Log::warning('StoreWasteVariantRequest: Validasi after() dilewati (awal).', [
        //                 'parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL',
        //                 'validator_fails' => $validator->fails()
        //             ]);
        //             return;
        //         }

        //         // Untuk sementara, komentari semua logika validasi stok agar fokus pada loop
        //         // $newVariantStockInput = $this->input('stock');
        //         // ... (sisa logika validasi stok Anda dikomentari) ...

        //         Log::info('StoreWasteVariantRequest: Closure di dalam after() SELESAI (tanpa validasi stok).', ['time' => microtime(true)]);
        //     }
        // ];

        // Log ini akan muncul SETIAP KALI method after() dipanggil
        Log::info('StoreWasteVariantRequest: Method after() DIPANGGIL.', ['time' => microtime(true)]);

        return [
            function (\Illuminate\Validation\Validator $validator) {
                // Log ini akan muncul SETIAP KALI CLOSURE di dalam after() dieksekusi
                Log::info('StoreWasteVariantRequest: Closure di dalam after() DIMULAI.', ['time' => microtime(true)]);

                /** @var \App\Models\Waste $parentWaste */
                $parentWaste = $this->route('waste');
                Log::info('StoreWasteVariantRequest: $parentWaste diambil dari route.', ['parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL']);

                if (!$parentWaste || $validator->fails()) {
                    Log::warning('StoreWasteVariantRequest: Validasi after() dilewati (awal).', [
                        'parent_waste_id' => $parentWaste ? $parentWaste->id : 'NULL',
                        'validator_fails' => $validator->fails()
                    ]);
                    return;
                }

                // Untuk sementara, komentari semua logika validasi stok agar fokus pada loop
                // $newVariantStockInput = $this->input('stock');
                // ... (sisa logika validasi stok Anda dikomentari) ...

                Log::info('StoreWasteVariantRequest: Closure di dalam after() SELESAI (tanpa validasi stok).', ['time' => microtime(true)]);
            }
        ];
    }
}
