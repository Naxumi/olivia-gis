<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Clickbar\Magellan\Data\Geometries\Point;

class WasteSearchController extends Controller
{
    /**
     * Mencari produk limbah dengan berbagai filter dan opsi urutan.
     * GET /api/wastes/search
     */
    public function search(Request $request): JsonResponse
    {
        // --- Validasi Input ---
        $validated = $request->validate([
            'q' => 'nullable|string|max:255', // Kata kunci pencarian nama waste
            'category' => 'nullable|string|exists:categories,name', // Filter by category name
            'status' => 'nullable|string|in:available,sold,expired', // Filter by status
            'sort_by' => 'nullable|string|in:nearby,price_asc,rating_desc,sold_desc', // Opsi pengurutan
            'latitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-90,90', // Wajib jika sort by nearby
            'longitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-180,180', // Wajib jika sort by nearby
        ]);

        // --- Membangun Query Dasar ---
        $query = Waste::query();

        // Join dengan tabel stores dan categories untuk filter dan data tambahan
        $query->join('stores', 'wastes.store_id', '=', 'stores.id');
        $query->join('categories', 'wastes.category_id', '=', 'categories.id');

        // Subquery untuk menghitung rata-rata rating per toko
        $reviewsSubQuery = DB::table('reviews')
            ->select('store_id', DB::raw('AVG(rating) as average_rating'))
            ->groupBy('store_id');

        // Left Join dengan subquery rating
        $query->leftJoinSub($reviewsSubQuery, 'reviews_avg', function ($join) {
            $join->on('stores.id', '=', 'reviews_avg.store_id');
        });

        // --- Memilih Kolom yang Akan Ditampilkan ---
        $query->select(
            'wastes.name as waste_name',
            'wastes.stock',
            'wastes.status',
            'wastes.price',
            'wastes.sold_count',
            'stores.name as store_name',
            'stores.location', // Kolom geography dari PostGIS
            DB::raw('COALESCE(reviews_avg.average_rating, 0) as average_rating') // Jika tidak ada rating, tampilkan 0
        );

        // --- Menerapkan Filter ---

        // Filter berdasarkan nama waste (query 'q')
        if (!empty($validated['q'])) {
            $query->where('wastes.name', 'ILIKE', '%' . $validated['q'] . '%'); // ILIKE untuk case-insensitive di PostgreSQL
        }

        // Filter berdasarkan nama kategori
        if (!empty($validated['category'])) {
            $query->where('categories.name', $validated['category']);
        }

        // Filter berdasarkan status
        if (!empty($validated['status'])) {
            $query->where('wastes.status', $validated['status']);
        }
        // Jika parameter 'status' tidak ada, semua status akan ditampilkan (default).

        // --- Menerapkan Pengurutan (Sorting) ---

        $sortBy = $validated['sort_by'] ?? null;

        if ($sortBy === 'nearby') {
            // Urutkan berdasarkan jarak terdekat dari lokasi pengguna
            // Pastikan latitude dan longitude pengguna tersedia dari request
            $userLocation = Point::makeGeodetic(
                latitude: (float)$validated['latitude'],
                longitude: (float)$validated['longitude']
            );

            // Menambahkan kolom virtual 'distance_km' ke query dan mengurutkannya
            // Menggunakan ST_Distance dengan casting ke geography untuk hasil dalam meter
            $query->selectRaw(
                "ST_Distance(stores.location, ?::geography) / 1000 AS distance_km",
                [$userLocation] // Laravel & Magellan akan menangani binding objek Point ini
            )->orderBy('distance_km', 'asc');

        } elseif ($sortBy === 'price_asc') {
            $query->orderBy('wastes.price', 'asc');
        } elseif ($sortBy === 'rating_desc') {
            $query->orderBy('average_rating', 'desc');
        } elseif ($sortBy === 'sold_desc') {
            $query->orderBy('wastes.sold_count', 'desc');
        } else {
            // Urutan default jika tidak ada parameter sort_by (misalnya, yang terbaru)
            $query->latest('wastes.created_at');
        }

        // --- Eksekusi Query dan Kembalikan Hasil ---
        $results = $query->get();

        return response()->json($results);
    }
}