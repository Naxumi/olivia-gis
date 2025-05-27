<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WasteController extends Controller
{
    /**
     * Display a listing of the resource for a specific store.
     *
     * @param  \App\Models\Store  $store
     * @return View
     */
    // public function index(Store $store): View
    // {
    //     if (Auth::id() !== $store->user_id && Auth::user()->role !== 'admin') {
    //         abort(403, 'Anda tidak diizinkan mengakses limbah toko ini.');
    //     }

    //     $wastes = $store->wastes()->with('category')->latest()->paginate(10);

    //     return view('wastes.index', compact('store', 'wastes'));
    // }

    public function index()
    {
        // Lokasi default peta, misalnya Jakarta

        $defaultLocation = ['lat' => -7.969627238985353, 'lng' => 112.60008006587819];
        return view('wastes.index', compact('defaultLocation'));
    }

    /**
     * Show the form for creating a new resource for a specific store.
     *
     * @param  \App\Models\Store  $store
     * @return View
     */
    public function create(Store $store): View
    {
        if (Auth::id() !== $store->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan menambah limbah untuk toko ini.');
        }

        $categories = Category::orderBy('name')->get();

        return view('wastes.create', compact('store', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Store  $store
     * @return RedirectResponse
     */
    public function store(Request $request, Store $store): RedirectResponse
    {
        if (Auth::id() !== $store->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak diizinkan menambah limbah untuk toko ini.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,sold,expired',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $store->wastes()->create($validatedData);

        return redirect()->route('stores.wastes.index', $store->id)
            ->with('success', 'Limbah berhasil ditambahkan ke toko ' . $store->name);
    }
}
