<?php

namespace App\Http\Controllers;

use App\Models\WasteVariant;
use App\Models\Category;
use App\Models\Store; // Untuk filter lokasi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk query Haversine jika perlu

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = WasteVariant::query()->with([
            'waste.store.user', // Seller info
            'waste.category',
            // 'reviews' // Jika ingin agregat rating atau menampilkan review
        ])->whereHas('waste', function ($q_waste) {
            $q_waste->where('status', 'available'); // Hanya yang tersedia
        });


        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->whereHas('waste.category', function ($q_cat) use ($request) {
                $q_cat->where('id', $request->category_id);
            });
        }

        // Filter Harga (range)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter Lokasi Terdekat (Contoh dengan Haversine - Perlu lat/lon user)
        // Anda perlu mendapatkan $userLatitude dan $userLongitude dari request atau Geolocation API
        if ($request->filled('user_latitude') && $request->filled('user_longitude')) {
            $userLat = $request->user_latitude;
            $userLon = $request->user_longitude;
            $radius = $request->input('radius', 25); // Radius dalam KM, default 25km

            // Rumus Haversine untuk PostgreSQL (atau lakukan di PHP jika lebih mudah)
            // Ini contoh, Anda mungkin perlu menyesuaikannya atau menggunakan PostGIS
            $query->whereHas('waste.store', function ($q_store) use ($userLat, $userLon, $radius) {
                $q_store->select('*')
                    ->selectRaw(
                        '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                        [$userLat, $userLon, $userLat]
                    )
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance', 'asc');
            });
        }

        // Filter Berada di Daerah Mana (jika alamat toko di-parse atau ada field daerah)
        if ($request->filled('region')) {
             $query->whereHas('waste.store', function ($q_store) use ($request) {
                $q_store->where('address', 'ILIKE', '%' . $request->region . '%'); // Pencarian sederhana
            });
        }

        // TODO: Filter Rating Tertinggi (perlu join/subquery dengan reviews dan group by)

        $wasteVariants = $query->paginate(15);
        $categories = Category::all();

        return view('marketplace.index', compact('wasteVariants', 'categories', 'request'));
    }
}