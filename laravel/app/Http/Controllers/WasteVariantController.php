<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\WasteVariant;
use App\Http\Requests\StoreWasteVariantRequest;   // 👈 Impor dan Gunakan ini
use App\Http\Requests\UpdateWasteVariantRequest;  // 👈 Impor dan Gunakan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Rules\WasteVariantStockRule;

class WasteVariantController extends Controller
{
    // ... (method create, store, edit, update Anda yang sudah ada) ...

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Waste  $waste // Parent Waste dari rute
     * @return \Illuminate\Http\Response
     */
    public function index(Waste $waste)
    {
        // Otorisasi: Pastikan user yang login adalah pemilik toko dari waste ini
        // atau seorang admin.
        // Asumsi model Waste memiliki relasi 'store' dan model Store memiliki 'user_id'
        if (Auth::id() !== $waste->store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan melihat varian untuk limbah ini.');
        }

        // Ambil semua varian yang terkait dengan waste ini, bisa diurutkan atau dipaginasi
        $variants = $waste->wasteVariants()
            ->orderBy('volume_in_grams', 'asc') // Contoh pengurutan
            ->paginate(10); // Contoh dengan paginasi

        // Kembalikan view untuk menampilkan daftar varian, kirim data $waste dan $variants
        return view('waste_variants.index', compact('waste', 'variants'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Waste  $waste         // Parent Waste dari rute
     * @param  \App\Models\WasteVariant  $variant   // Varian yang akan ditampilkan dari rute
     * @return \Illuminate\Http\Response
     */
    public function show(Waste $waste, WasteVariant $variant)
    {
        // Otorisasi: Pastikan user adalah pemilik toko dari waste ini
        // dan bahwa varian ini memang milik waste tersebut.
        // Juga, admin mungkin diizinkan.
        if (Auth::id() !== $waste->store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan melihat detail varian limbah ini.');
        }

        // Pastikan varian yang diminta memang milik waste yang benar (pengaman tambahan)
        if ($variant->waste_id !== $waste->id) {
            abort(404); // Atau redirect dengan pesan error
        }

        // Kembalikan view untuk menampilkan detail varian, kirim data $waste dan $variant
        return view('waste_variants.show', compact('waste', 'variant'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Waste  $waste // 👈 UBAH INI dari $wasteId menjadi Waste $waste
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create(Waste $waste)
    {
        // Otorisasi untuk menampilkan form (bisa juga menggunakan Policy)
        if (Auth::id() !== $waste->store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            // Tambahkan pengecekan admin jika admin boleh melakukan ini
            abort(403, 'Anda tidak diizinkan menambah varian untuk limbah ini.');
        }

        return view('waste_variants.create', compact('waste'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreWasteVariantRequest $request, Waste $waste) // 👈 Gunakan StoreWasteVariantRequest
    // {
    //     // // Otorisasi dan validasi (termasuk validasi stok vs parent)
    //     // // sudah otomatis dilakukan oleh StoreWasteVariantRequest.
    //     // // Jika gagal, user akan otomatis di-redirect kembali ke form dengan pesan error.

    //     // $validatedData = $request->validated(); // Ambil data yang sudah bersih dan tervalidasi

    //     // $waste->wasteVariants()->create($validatedData);

    //     // // Redirect ke halaman yang sesuai, misalnya daftar varian untuk waste tersebut
    //     // // atau ke detail waste dari parent storenya.
    //     // return redirect()->route('stores.wastes.show', ['store' => $waste->store_id, 'waste' => $waste->id])
    //     //     ->with('success', 'Varian limbah berhasil ditambahkan untuk ' . $waste->name);


    //     // ATTEMPT TWO

    //     // Debug: cek apakah blok ini dieksekusi
    //     logger('StoreWasteVariantRequest executed'); // Laravel log
    //     // Atau, jika ingin benar-benar "print" ke browser (tidak disarankan di production):
    //     echo "StoreWasteVariantRequest executed"; exit;

    //     $validatedData = $request->validate([
    //         'waste_id' => 'required|exists:wastes,id',
    //         'volume_in_grams' => 'required|integer|min:1',
    //         'price' => 'required|numeric|min:0',
    //         'stock' => [
    //         'required',
    //         'integer',
    //         'min:0', // atau min:1 jika tidak boleh 0
    //         new WasteVariantStockRule($request->input('waste_id'))
    //         ],
    //     ]);

    //     // ... logika penyimpanan WasteVariant
    //     $wasteVariant = WasteVariant::create($validatedData);
    //     return response()->json($wasteVariant, 201);
    // }

    // public function store(Request $request)
    // {

    //     logger('StoreWasteVariantRequest executed'); // Laravel log
    //     echo "StoreWasteVariantRequest executed";
    //     $validatedData = $request->validate([
    //         'waste_id' => 'required|exists:wastes,id',
    //         'volume_in_grams' => 'required|integer|min:1',
    //         'price' => 'required|numeric|min:0',
    //         'stock' => [
    //             'required',
    //             'integer',
    //             'min:0', // atau min:1 jika tidak boleh 0
    //             new WasteVariantStockRule($request->input('waste_id'))
    //         ],
    //     ]);

    //     // ... logika penyimpanan WasteVariant
    //     $wasteVariant = WasteVariant::create($validatedData);
    //     return response()->json($wasteVariant, 201);
    // }

    public function store(Request $request, Waste $waste) // Atau StoreWasteVariantRequest $request jika Anda sudah siap
    {
        Log::info('Processing request to store waste variant for Waste ID: ' . $waste->id . ' by User ID: ' . (Auth::id() ?? 'Guest'));

        // --- MULAI BLOK OTORISASI ---
        $user = Auth::user();

        // Periksa apakah pengguna terautentikasi
        if (!$user) {
            // Untuk API, biasanya mengembalikan 401 Unauthorized
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Cek apakah $waste memiliki store dan store memiliki user_id
        // Ini penting untuk menghindari error jika relasi tidak lengkap
        if (!$waste->store || !$waste->store->user_id) {
            Log::error("Waste ID: {$waste->id} does not have a properly linked store or store owner.");
            // Anda bisa memilih untuk abort atau mengembalikan error spesifik
            return response()->json(['message' => 'Konfigurasi limbah atau toko tidak lengkap.'], 500);
        }

        $isOwner = ($waste->store->user_id === $user->id);
        $isAdmin = $user->roles()->where('name', 'admin')->exists();

        if (!$isAdmin && !$isOwner) {
            Log::warning("Unauthorized attempt to store waste variant for Waste ID: {$waste->id} by User ID: {$user->id}");
            return response()->json(['message' => 'Anda tidak diizinkan menambah varian untuk limbah ini.'], 403); // 403 Forbidden
        }
        // --- SELESAI BLOK OTORISASI ---

        Log::info("User ID: {$user->id} (Admin: {$isAdmin}, Owner: {$isOwner}) authorized to store variant for Waste ID: {$waste->id}");

        $validatedData = $request->validate([
            'volume_in_grams' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'stock' => [
                'required',
                'integer',
                'min:0',
                new WasteVariantStockRule($waste->id, null)
            ],
        ]);

        $dataToCreate = $validatedData;
        $dataToCreate['waste_id'] = $waste->id;

        $wasteVariant = WasteVariant::create($dataToCreate);

        Log::info("Successfully stored WasteVariant ID: {$wasteVariant->id} for Waste ID: {$waste->id}");

        return response()->json($wasteVariant, 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Waste  $waste         // Parent Waste dari rute
     * @param  \App\Models\WasteVariant  $variant   // Varian yang akan diedit dari rute
     * @return \Illuminate\Http\Response
     */
    public function edit(Waste $waste, WasteVariant $variant)
    {
        $user = Auth::user();

        // 1. Periksa autentikasi dasar
        if (!$user) {
            // Untuk aplikasi web standar, abort akan menampilkan halaman error yang sesuai.
            abort(401, 'Unauthenticated.');
        }

        // 2. Integritas Data: Pastikan varian benar-benar milik waste dari rute
        if ($variant->waste_id !== $waste->id) {
            Log::warning("Integrity check failed in EDIT: Variant ID {$variant->id} (waste_id {$variant->waste_id}) does not match parent Waste ID {$waste->id} in route. User ID: {$user->id}");
            abort(404, 'Varian tidak ditemukan atau tidak sesuai dengan limbah induk.');
        }

        // 3. Cek kelengkapan data store untuk otorisasi
        //    Penting untuk memeriksa keberadaan $waste->store sebelum mengakses ->user_id
        if (!$waste->store || !$waste->store->user_id) {
            Log::error("Data integrity issue for EDIT authorization: Waste ID: {$waste->id} does not have a properly linked store or store owner. User ID: {$user->id}");
            // Menampilkan error server karena ini masalah konfigurasi data
            abort(500, 'Konfigurasi limbah atau toko tidak lengkap untuk otorisasi.');
        }

        // 4. Lakukan pengecekan peran (admin) atau kepemilikan toko
        $isOwner = ($waste->store->user_id === $user->id);
        $isAdmin = $user->roles()->where('name', 'admin')->exists(); // Asumsi relasi roles() dan nama peran 'admin'

        if (!$isAdmin && !$isOwner) {
            Log::warning("Unauthorized attempt to EDIT waste variant ID: {$variant->id} for Waste ID: {$waste->id} by User ID: {$user->id}");
            abort(403, 'Anda tidak diizinkan mengedit varian limbah ini.');
        }

        Log::info("User ID: {$user->id} (Admin: {$isAdmin}, Owner: {$isOwner}) authorized to EDIT variant ID: {$variant->id} for Waste ID: {$waste->id}");

        return view('waste_variants.edit', compact('waste', 'variant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Waste  $waste
     * @param  \App\Models\WasteVariant  $variant
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Waste $waste, WasteVariant $variant) // 👈 Ubah ke Illuminate\Http\Request
    {
        Log::info('Processing request to update waste variant ID: ' . $variant->id . ' for Waste ID: ' . $waste->id . ' by User ID: ' . (Auth::id() ?? 'Guest'));

        // --- MULAI BLOK OTORISASI ---
        $user = Auth::user();

        // 1. Periksa apakah pengguna terautentikasi
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 2. Integritas Data: Pastikan varian yang diupdate benar-benar milik waste dari rute
        if ($variant->waste_id !== $waste->id) {
            Log::warning("Integrity check failed: Variant ID {$variant->id} (belongs to waste_id {$variant->waste_id}) does not match parent Waste ID {$waste->id} in route. User ID: {$user->id}");
            // Bisa juga abort(404) jika Anda menganggap ini 'Not Found' dari perspektif pengguna
            return response()->json(['message' => 'Data varian tidak sesuai atau tidak ditemukan.'], 404);
        }

        // 3. Cek kelengkapan data store untuk otorisasi
        if (!$waste->store || !$waste->store->user_id) {
            Log::error("Data integrity issue for update authorization: Waste ID: {$waste->id} does not have a properly linked store or store owner. User ID: {$user->id}");
            return response()->json(['message' => 'Konfigurasi limbah atau toko tidak lengkap untuk otorisasi.'], 500);
        }

        // 4. Lakukan pengecekan peran (admin) atau kepemilikan toko
        $isOwner = ($waste->store->user_id === $user->id);
        $isAdmin = $user->roles()->where('name', 'admin')->exists();

        if (!$isAdmin && !$isOwner) {
            Log::warning("Unauthorized attempt to update waste variant ID: {$variant->id} for Waste ID: {$waste->id} by User ID: {$user->id}");
            return response()->json(['message' => 'Anda tidak diizinkan memperbarui varian untuk limbah ini.'], 403);
        }
        // --- SELESAI BLOK OTORISASI ---

        Log::info("User ID: {$user->id} (Admin: {$isAdmin}, Owner: {$isOwner}) authorized to update variant ID: {$variant->id} for Waste ID: {$waste->id}");

        // --- MULAI BLOK VALIDASI ---
        $validatedData = $request->validate([
            'volume_in_grams' => 'sometimes|required|integer|min:1', // 'sometimes' karena mungkin tidak semua field diupdate
            'price'           => 'sometimes|required|numeric|min:0',
            'stock'           => [
                'sometimes',
                'required', // Jika 'stock' dikirim, maka harus ada nilainya
                'integer',
                'min:0',
                new WasteVariantStockRule($waste->id, $variant->id) // Kirim $variant->id sebagai $currentVariantId
            ],
            // Tambahkan field lain yang bisa diupdate di sini jika ada
        ]);
        // --- SELESAI BLOK VALIDASI ---

        // Lakukan update hanya jika ada data yang tervalidasi
        if (count($validatedData) > 0) {
            $variant->update($validatedData);
            Log::info("WasteVariant ID: {$variant->id} successfully updated with data: ", $validatedData);
        } else {
            Log::info("No data provided to update for WasteVariant ID: {$variant->id}");
            // Anda mungkin ingin mengembalikan respons berbeda jika tidak ada data yang diupdate,
            // atau tetap redirect dengan pesan bahwa tidak ada perubahan.
            // Untuk saat ini, kita biarkan alur redirect tetap berjalan.
        }

        // Respons Anda saat ini adalah redirect, yang cocok untuk aplikasi web.
        // Jika ini murni API, Anda mungkin ingin mengembalikan JSON:
        // return response()->json($variant->fresh()); // Mengambil data terbaru dari varian
        return redirect()->route('stores.wastes.show', ['store' => $waste->store_id, 'waste' => $waste->id])
            ->with('success', 'Varian limbah untuk ' . $waste->name . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Waste  $waste         // Parent Waste dari rute
     * @param  \App\Models\WasteVariant  $variant   // Varian yang akan dihapus dari rute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Waste $waste, WasteVariant $variant)
    {
        // Otorisasi: Pastikan user adalah pemilik toko dari waste ini
        // dan bahwa varian ini memang milik waste tersebut.
        // Juga, admin mungkin diizinkan.
        if (Auth::id() !== $waste->store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan menghapus varian limbah ini.');
        }

        // Pastikan varian yang akan dihapus memang milik waste yang benar (pengaman tambahan)
        if ($variant->waste_id !== $waste->id) {
            abort(404); // Atau redirect dengan pesan error
        }

        // Sebelum menghapus, Anda mungkin ingin melakukan pengecekan lain,
        // misalnya apakah varian ini sedang dalam transaksi aktif.
        // Untuk saat ini, kita langsung hapus.
        $variant->delete();

        // Redirect kembali ke daftar varian untuk waste tersebut dengan pesan sukses.
        return redirect()->route('wastes.variants.index', $waste->id)
            ->with('success', 'Varian limbah (Volume: ' . number_format($variant->volume_in_grams, 0, ',', '.') . ' gram) berhasil dihapus.');
    }
}
