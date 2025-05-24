<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteController extends Controller
{

    /**
     * Display a listing of the resource for a specific store.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function index(Store $store) // 👈 Terima $store melalui Route Model Binding
    {
        // Otorisasi: Pastikan user yang login adalah pemilik toko atau admin
        // Anda bisa menggunakan Policy di sini jika sudah dibuat.
        // Contoh sederhana:
        if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan mengakses limbah toko ini.');
        }

        // Ambil semua limbah yang terkait dengan toko ini
        // Anda bisa menambahkan ->with('category') jika ingin menampilkan nama kategori
        // dan ->latest() atau orderBy() lainnya jika perlu
        $wastes = $store->wastes()->with('category')->latest()->paginate(10); // Contoh dengan paginasi

        // Kembalikan view untuk menampilkan daftar limbah, kirim data $store dan $wastes
        return view('wastes.index', compact('store', 'wastes'));
    }

    /**
     * Show the form for creating a new resource for a specific store.
     *
     * @param  \App\Models\Store  $store  // 👈 UBAH INI: Gunakan type hint Store $store
     * @return \Illuminate\Http\Response
     */
    public function create(Store $store) // 👈 UBAH INI: Laravel akan otomatis inject objek Store
    {
        // Sekarang $store adalah instance dari model Store, bukan hanya ID
        if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan menambah limbah untuk toko ini.');
        }

        // Ambil daftar kategori untuk ditampilkan di form
        $categories = Category::orderBy('name')->get();

        // Kembalikan view untuk membuat limbah baru, kirim data $store (objek) dan $categories
        return view('wastes.create', compact('store', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store // 👈 UBAH INI dari $storeId menjadi Store $store
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Store $store) // 👈 Perubahan di sini
    {
        // Otorisasi: Pastikan user yang login adalah pemilik toko atau admin
        if (Auth::id() !== $store->user_id && !Auth::user()->roles()->where('name', 'admin')->exists()) {
            abort(403, 'Anda tidak diizinkan menambah limbah untuk toko ini.');
        }

        $validatedData = $request->validate([ // Simpan hasil validasi ke variabel
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0', // Stok utama untuk Waste
            'status' => 'required|in:available,sold,expired', // Pastikan nilainya sesuai enum
            'price' => 'required|numeric|min:0',           // Harga dasar untuk Waste
            'description' => 'nullable|string',
        ]);

        // Buat waste baru yang terkait dengan $store yang sudah di-inject
        $waste = $store->wastes()->create($validatedData); // Gunakan $validatedData

        // Redirect ke halaman detail toko atau daftar limbah toko tersebut
        return redirect()->route('stores.wastes.index', $store->id)->with('success', 'Limbah berhasil ditambahkan ke toko ' . $store->name);
    }
}
