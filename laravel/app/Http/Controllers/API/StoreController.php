<?php

namespace App\Http\Controllers\API; // Namespace disesuaikan

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User; // Untuk type-hinting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // Jika perlu DB transaction
use Clickbar\Magellan\Data\Geometries\Point; // Dari clickbar/laravel-magellan
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Validation\Rules\File; // Import aturan validasi File

class StoreController extends Controller
{
    // /**
    //  * Menampilkan daftar toko.
    //  * GET /api/stores
    //  */
    // public function index(Request $request): JsonResponse
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();
    //     $itemsPerPage = $request->query('per_page', 10);

    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthenticated.'], 401);
    //     }

    //     $query = Store::query()->with('user:id,name'); // Eager load pemilik toko

    //     // Logika untuk menampilkan toko:
    //     // Jika admin, tampilkan semua. Jika seller, tampilkan tokonya.
    //     // Buyer mungkin bisa lihat semua (tergantung kebutuhan marketplace Anda).
    //     if ($user->hasRole('admin')) {
    //         // Admin melihat semua toko
    //         $stores = $query->latest()->paginate($itemsPerPage);
    //     } elseif ($user->hasRole('seller')) {
    //         // Seller hanya melihat tokonya sendiri
    //         $stores = $user->stores()->with('user:id,name')->latest()->paginate($itemsPerPage);
    //     } elseif ($user->hasRole('buyer') || $user->hasRole('distributor')) {
    //         // Buyer dan Distributor bisa melihat semua toko (untuk marketplace)
    //         // Jika Anda ingin membatasi ini, tambahkan logika di sini.
    //         $stores = $query->latest()->paginate($itemsPerPage);
    //     } else {
    //         // Untuk role lain atau jika tidak ada role yang cocok, kembalikan koleksi kosong atau error
    //         return response()->json(['message' => 'Anda tidak memiliki akses untuk melihat daftar toko ini.'], 403);
    //     }

    //     return response()->json($stores);
    // }

    /**
     * Menampilkan daftar semua toko yang relevan.
     * GET /api/stores
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Store::query()->with('user:id,name'); // Eager load pemilik toko

        // Logika untuk menampilkan toko:
        // Jika admin, tampilkan semua. Jika seller, tampilkan tokonya.
        // Buyer dan Distributor bisa melihat semua toko.
        if ($user->hasRole('admin')) {
            // Admin melihat semua toko
            $stores = $query->latest()->get(); // Diubah dari paginate() menjadi get()
        } elseif ($user->hasRole('seller')) {
            // Seller hanya melihat tokonya sendiri
            $stores = $user->stores()->with('user:id,name')->latest()->get(); // Diubah dari paginate() menjadi get()
        } elseif ($user->hasRole('buyer') || $user->hasRole('distributor')) {
            // Buyer dan Distributor bisa melihat semua toko (untuk marketplace)
            $stores = $query->latest()->get(); // Diubah dari paginate() menjadi get()
        } else {
            // Untuk role lain atau jika tidak ada role yang cocok, kembalikan koleksi kosong.
            return response()->json([]); // Mengembalikan array kosong jika tidak ada akses
        }

        return response()->json($stores);
    }

    /**
     * Menyimpan toko baru dengan gambar.
     * POST /api/stores
     */
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->hasRole('seller')) {
            return response()->json(['message' => 'Hanya seller yang dapat membuat toko.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            // Validasi untuk file gambar
            'image' => ['nullable', File::image()->max(2048)], // Opsional, gambar, maks 2MB
        ]);

        try {
            $storeData = [
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'location' => Point::makeGeodetic(
                    latitude: (float)$validatedData['latitude'],
                    longitude: (float)$validatedData['longitude']
                ),
            ];

            // Proses upload gambar jika ada
            if ($request->hasFile('image')) {
                // Simpan file ke storage/app/public/store-images dan dapatkan path-nya
                $path = $request->file('image')->store('store-images', 'public');
                $storeData['image_path'] = $path;
            }

            $store = $user->stores()->create($storeData);

            return response()->json([
                'message' => 'Toko berhasil dibuat!',
                'store' => $store->load('user:id,name')
            ], 201);
        } catch (\Exception $e) {
            Log::error("Gagal membuat toko oleh user {$user->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat toko.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail satu toko.
     * GET /api/stores/{store}
     */
    public function show(Store $store): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Otorisasi: Pemilik toko atau admin bisa melihat detail.
        // Buyer/Distributor juga bisa melihat detail toko publik.
        $canView = false;
        if ($user->id === $store->user_id || $user->hasRole('admin') || $user->hasRole('buyer') || $user->hasRole('distributor')) {
            $canView = true;
        }

        if (!$canView) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk melihat toko ini.'], 403);
        }

        // Eager load relasi yang mungkin dibutuhkan oleh SPA
        $store->load(['user:id,name', 'wastes', 'wastes.category', 'wastes.wasteVariants']);

        return response()->json($store);
    }

    /**
     * Memperbarui data toko yang sudah ada, termasuk gambar.
     * PENTING: Untuk update file, request dari frontend harus menggunakan method POST
     * dan menyertakan field _method dengan nilai 'PUT' atau 'PATCH' (form-data).
     * Atau, buat endpoint terpisah khusus untuk update gambar.
     *
     * POST /api/stores/{store}  (dengan _method="PATCH" di body)
     */
    public function update(Request $request, Store $store): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!($user->id === $store->user_id || $user->hasRole('admin'))) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk mengupdate toko ini.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:1000',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|sometimes|numeric|between:-180,180',
            'image' => ['nullable', File::image()->max(2048)],
        ]);

        try {
            $updateData = [];
            if ($request->has('name')) $updateData['name'] = $validatedData['name'];
            if ($request->has('address')) $updateData['address'] = $validatedData['address'];
            if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
                $updateData['location'] = Point::makeGeodetic(
                    latitude: (float)$validatedData['latitude'],
                    longitude: (float)$validatedData['longitude']
                );
            }

            // Proses upload gambar baru jika ada
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika ada
                if ($store->image_path) {
                    Storage::disk('public')->delete($store->image_path);
                }
                // Simpan gambar baru
                $path = $request->file('image')->store('store-images', 'public');
                $updateData['image_path'] = $path;
            }

            if (empty($updateData)) {
                return response()->json([
                    'message' => 'Tidak ada data valid yang dikirim untuk diperbarui.',
                    'store' => $store->load('user:id,name')
                ]);
            }

            $store->update($updateData);

            return response()->json([
                'message' => 'Toko berhasil diperbarui!',
                'store' => $store->fresh()->load('user:id,name')
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal update toko {$store->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui toko.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus toko.
     * DELETE /api/stores/{store}
     */
    public function destroy(Store $store): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Otorisasi: Hanya pemilik toko atau admin yang bisa hapus
        if (!($user->id === $store->user_id && $user->hasRole('seller')) && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk menghapus toko ini.'], 403);
        }

        try {
            // onDelete('cascade') pada foreign key di tabel wastes akan menangani penghapusan waste terkait.
            $store->delete();
            return response()->json(['message' => 'Toko berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus toko {$store->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus toko.', 'error' => $e->getMessage()], 500);
        }
    }
}
