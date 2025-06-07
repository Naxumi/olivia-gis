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
use Illuminate\Http\JsonResponse;

class WasteVariantController extends Controller
{
    // ... (method create, store, edit, update Anda yang sudah ada) ...

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Waste  $waste // Parent Waste dari rute
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Waste $waste): JsonResponse
    {
        // Otorisasi: Pastikan user yang login adalah pemilik toko dari waste ini
        // atau seorang admin.
        // Asumsi model Waste memiliki relasi 'store' dan model Store memiliki 'user_id'
        // if (Auth::id() !== $waste->store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
        //     abort(403, 'Anda tidak diizinkan melihat varian untuk limbah ini.');
        // }

        // // Ambil semua varian yang terkait dengan waste ini, bisa diurutkan atau dipaginasi
        // $variants = $waste->wasteVariants()
        //     ->orderBy('volume_in_grams', 'asc') // Contoh pengurutan
        //     ->paginate(10); // Contoh dengan paginasi

        // // Kembalikan view untuk menampilkan daftar varian, kirim data $waste dan $variants
        // return view('waste_variants.index', compact('waste', 'variants'));

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Otorisasi: Pastikan user yang login adalah pemilik toko dari waste ini
        // atau seorang admin.
        if (!$user || ($user->id !== $waste->store->user_id && !$user->hasRole('admin'))) {
            return response()->json(['message' => 'Anda tidak diizinkan melihat varian untuk limbah ini.'], 403);
        }

        // Ambil semua varian yang terkait dengan waste ini, diurutkan, dan dipaginasi.
        // Jumlah item per halaman bisa diambil dari request.
        $itemsPerPage = $request->query('per_page', 10);

        $variants = $waste->wasteVariants()
            ->orderBy('volume_in_grams', 'asc') // Contoh pengurutan
            ->paginate($itemsPerPage);

        return response()->json($variants);
    }

    /**
     * Menampilkan detail satu varian limbah spesifik.
     * GET /api/wastes/{waste}/variants/{variant}  atau  /api/waste-variants/{variant}
     */
    public function show(Waste $waste, WasteVariant $variant): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // --- Otorisasi ---
        // 1. Integritas Data: Pastikan varian yang diminta benar-benar milik dari waste induk di URL.
        if ($variant->waste_id !== $waste->id) {
            return response()->json(['message' => 'Varian tidak ditemukan untuk produk limbah ini.'], 404);
        }

        // 2. Hak Akses: Siapa saja bisa melihat detail varian (untuk marketplace),
        //    jadi kita tidak perlu membatasi berdasarkan peran di sini.
        //    Jika Anda ingin membatasi (misalnya hanya pemilik atau admin), Anda bisa tambahkan logikanya:
        //
        // if (!$user || ($user->id !== $waste->store->user_id && !$user->hasRole('admin'))) {
        //     return response()->json(['message' => 'Anda tidak diizinkan melihat detail varian ini.'], 403);
        // }


        // --- Eager Load Relasi ---
        // Muat relasi ke waste induk dan kategori dari waste induk tersebut
        $variant->load(['waste.category', 'waste.store:id,name']);


        // --- Kembalikan Respons JSON ---
        return response()->json($variant);
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
     * Memperbarui data varian limbah yang sudah ada.
     * PUT/PATCH /api/wastes/{waste}/variants/{variant}
     */
    public function update(Request $request, Waste $waste, WasteVariant $variant): JsonResponse
    {
        Log::info('Processing request to update waste variant ID: ' . $variant->id . ' for Waste ID: ' . $waste->id . ' by User ID: ' . (Auth::id() ?? 'Guest'));

        // --- MULAI BLOK OTORISASI ---
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 1. Integritas Data: Pastikan varian yang diupdate benar-benar milik waste dari rute
        if ($variant->waste_id !== $waste->id) {
            return response()->json(['message' => 'Data varian tidak sesuai atau tidak ditemukan.'], 404);
        }

        // 2. Otorisasi Peran: Pastikan user adalah pemilik toko atau admin
        if (!($waste->store->user_id === $user->id || $user->hasRole('admin'))) {
            return response()->json(['message' => 'Anda tidak diizinkan memperbarui varian untuk limbah ini.'], 403);
        }
        // --- SELESAI BLOK OTORISASI ---

        // --- MULAI BLOK VALIDASI ---
        $validatedData = $request->validate([
            'volume_in_grams' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                new WasteVariantStockRule($waste->id, $variant->id) // Rule kustom untuk cek stok
            ],
        ]);
        // --- SELESAI BLOK VALIDASI ---

        if (empty($validatedData)) {
            return response()->json([
                'message' => 'Tidak ada data valid yang dikirim untuk diperbarui.',
                'variant' => $variant
            ]);
        }

        try {
            $variant->update($validatedData);
            Log::info("WasteVariant ID: {$variant->id} successfully updated with data: ", $validatedData);

            // --- PERUBAHAN UTAMA: GANTI REDIRECT DENGAN RESPON JSON ---
            return response()->json([
                'message' => 'Varian limbah berhasil diperbarui!',
                'variant' => $variant->fresh() // Ambil data terbaru dari database
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal update varian limbah {$variant->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui varian limbah.', 'error' => $e->getMessage()], 500);
        }
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
