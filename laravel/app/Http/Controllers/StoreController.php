<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point dari Magellan
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    use AuthorizesRequests;



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $itemsPerPage = 10; // Tentukan jumlah item per halaman, bisa juga dari config atau request

        // Logika yang sudah ada: Tampilkan toko milik user yang login
        $stores = $user->stores()->latest()->paginate($itemsPerPage);

        // Jika Anda ingin mengimplementasikan "atau semua toko jika admin":
        // if ($user->isAdmin()) { // Asumsi Anda punya method isAdmin() di model User atau cara lain untuk cek role
        //     $stores = \App\Models\Store::latest()->paginate($itemsPerPage); // Ambil semua toko untuk admin
        // } else {
        //     $stores = $user->stores()->latest()->paginate($itemsPerPage);
        // }

        return view('stores.index', compact('stores'));
    }
    // public function index()
    // {
    //     // Tampilkan toko milik seller yang login, atau semua toko jika admin
    //     /** @var \App\Models\User $user */ // Type hint untuk Intelephense
    //     $stores = Auth::user()->stores()->latest()->get();
    //     return view('stores.index', compact('stores'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pastikan user adalah seller, bisa juga dicek dengan Gate/Policy
        if (!Auth::user()->roles()->where('name', 'seller')->exists()) {
            abort(403, 'Hanya seller yang dapat membuat toko.');
        }
        return view('stores.create');
    }

    /**
     * Menyimpan toko baru. Hanya untuk Seller.
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
            // Validasi untuk input latitude dan longitude terpisah
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        try {
            // Buat objek Point dari Magellan
            $locationPoint = Point::makeGeodetic(
                latitude: (float)$validatedData['latitude'],
                longitude: (float)$validatedData['longitude']
            );

            // Simpan toko baru dengan objek Point
            $store = $user->stores()->create([
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'location' => $locationPoint, // Menyimpan objek Point
            ]);

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
     * Display the specified resource.
     */
    public function show(Store $store)
    {

        if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan untuk melihat toko ini.');
        }
        // Anda mungkin ingin eager load relasi lain di sini, e.g. $store->load('wastes.wasteVariants');
        return view('stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {

        // Jika user bukan seller pemilik toko ini, dan bukan admin
        if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit toko ini.');
        }

        return view('stores.edit', compact('store'));
    }

    /**
     * Memperbarui data toko yang sudah ada. Hanya untuk pemilik atau Admin.
     * PUT/PATCH /api/stores/{store}
     */
    public function update(Request $request, Store $store): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Otorisasi: Hanya pemilik toko atau admin yang bisa update
        if (!($user->id === $store->user_id || $user->hasRole('admin'))) {
            return response()->json(['message' => 'Anda tidak diizinkan untuk mengupdate toko ini.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:1000',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            // Longitude wajib ada jika latitude dikirim
            'longitude' => 'required_with:latitude|sometimes|numeric|between:-180,180',
        ]);

        try {
            $updateData = [];

            // Tambahkan data non-lokasi ke array update
            if ($request->has('name')) $updateData['name'] = $validatedData['name'];
            if ($request->has('address')) $updateData['address'] = $validatedData['address'];

            // Cek jika data lokasi dikirim, lalu buat objek Point
            if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
                $updateData['location'] = Point::makeGeodetic(
                    latitude: (float)$validatedData['latitude'],
                    longitude: (float)$validatedData['longitude']
                );
            }

            if (empty($updateData)) {
                return response()->json(['message' => 'Tidak ada data valid yang dikirim untuk diperbarui.', 'store' => $store]);
            }

            $store->update($updateData);

            return response()->json([
                'message' => 'Toko berhasil diperbarui!',
                'store' => $store->fresh()->load('user:id,name') // Ambil data terbaru
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal update toko {$store->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui toko.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {

        if (Auth::id() !== $store->user_id) {
            abort(403, 'Anda tidak diizinkan untuk menghapus toko ini.');
        }

        // Sebelum menghapus store, Anda mungkin perlu menangani relasi lain,
        // misalnya menghapus semua 'wastes' yang terkait jika onDelete('cascade') tidak diatur
        // atau jika ada logika bisnis lain.
        // Namun, karena migrasi Anda sudah ada onDelete('cascade') untuk 'wastes' pada 'store_id',
        // maka 'wastes' akan otomatis terhapus.

        $store->delete();

        return redirect()->route('stores.index')->with('success', 'Toko berhasil dihapus!');
    }
}
