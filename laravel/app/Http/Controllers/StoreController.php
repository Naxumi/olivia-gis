<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->roles()->where('name', 'seller')->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Auth::user()->stores()->create($validated); // Langsung assign user_id

        return redirect()->route('stores.index')->with('success', 'Toko berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        $this->authorize('view', $store); // Menggunakan StorePolicy

        // if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
        //     abort(403, 'Anda tidak diizinkan untuk melihat toko ini.');
        // }
        // Anda mungkin ingin eager load relasi lain di sini, e.g. $store->load('wastes.wasteVariants');
        return view('stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        $this->authorize('update', $store); // Menggunakan StorePolicy

        // Jika user bukan seller pemilik toko ini, dan bukan admin
        // if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
        //     abort(403, 'Anda tidak diizinkan untuk mengedit toko ini.');
        // }

        return view('stores.edit', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $this->authorize('update', $store); // Menggunakan StorePolicy

        // if (Auth::id() !== $store->user_id) {
        //     abort(403, 'Anda tidak diizinkan untuk mengupdate toko ini.');
        // }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $store->update($validated);

        return redirect()->route('stores.index')->with('success', 'Toko berhasil diperbarui!');
        // Atau redirect ke stores.show:
        // return redirect()->route('stores.show', $store->id)->with('success', 'Toko berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $this->authorize('delete', $store); // Menggunakan StorePolicy

        // if (Auth::id() !== $store->user_id) {
        //     abort(403, 'Anda tidak diizinkan untuk menghapus toko ini.');
        // }

        // Sebelum menghapus store, Anda mungkin perlu menangani relasi lain,
        // misalnya menghapus semua 'wastes' yang terkait jika onDelete('cascade') tidak diatur
        // atau jika ada logika bisnis lain.
        // Namun, karena migrasi Anda sudah ada onDelete('cascade') untuk 'wastes' pada 'store_id',
        // maka 'wastes' akan otomatis terhapus.

        $store->delete();

        return redirect()->route('stores.index')->with('success', 'Toko berhasil dihapus!');
    }
}
