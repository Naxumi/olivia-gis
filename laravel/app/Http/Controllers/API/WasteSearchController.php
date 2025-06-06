<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Waste;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Validation\ValidationException;
use Throwable;

// Impor kelas-kelas yang diperlukan dari Magellan
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Clickbar\Magellan\Database\Expressions\AsGeometry; // <-- PASTIKAN ANDA MENGIMPOR INI

class WasteSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        try {
            // --- 1. Validasi Input ---
            $validated = $request->validate([
                'q' => 'nullable|string|max:255',
                'category' => 'nullable|string',
                'status' => 'nullable|string|in:available,sold,expired',
                'sort_by' => 'nullable|string|in:nearby,price_asc,rating_desc,sold_desc',
                'latitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-90,90',
                'longitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-180,180',
            ]);

            // --- 2. Query Dasar ---
            $query = Waste::query()
                ->join('stores', 'wastes.store_id', '=', 'stores.id')
                ->join('categories', 'wastes.category_id', '=', 'categories.id')
                ->whereNotNull('stores.location');

            // Subquery untuk rating
            $reviewsSubQuery = DB::table('reviews')
                ->select('store_id', DB::raw('AVG(rating) as average_rating'))
                ->groupBy('store_id');
            $query->leftJoinSub($reviewsSubQuery, 'reviews_avg', 'stores.id', '=', 'reviews_avg.store_id');

            // --- 3. Kolom Pilihan ---
            $query->select(
                'wastes.id as waste_id', 'wastes.name as waste_name', 'wastes.stock', 'wastes.status', 'wastes.price', 'wastes.sold_count',
                'stores.id as store_id', 'stores.name as store_name',
                
                // --- PERBAIKAN UTAMA: GUNAKAN AsGeometry UNTUK CASTING ---
                // Ini akan mengubah SQL menjadi ST_Y(("stores"."location")::geometry)
                // yang akan diterima oleh PostgreSQL
                ST::y(new AsGeometry('stores.location'))->as('latitude'),
                ST::x(new AsGeometry('stores.location'))->as('longitude'),
                
                DB::raw('COALESCE(reviews_avg.average_rating, 0) as average_rating')
            );

            // --- 4. Filter (sudah benar) ---
            if (!empty($validated['q'])) {
                $query->where('wastes.name', 'ILIKE', '%' . $validated['q'] . '%');
            }

            // --- 5. Pengurutan (sudah benar) ---
            $sortBy = $validated['sort_by'] ?? null;
            if ($sortBy === 'nearby' && isset($validated['latitude'], $validated['longitude'])) {
                $userLocation = Point::makeGeodetic(
                    latitude: (float) $validated['latitude'],
                    longitude: (float) $validated['longitude']
                );
                
                // Scope ini akan bekerja dengan tipe geography, jadi tidak perlu diubah
                 $query->withDistance('location', $userLocation, 'distance_km')
                       ->orderByDistance('location', $userLocation, 'asc');

            } elseif ($sortBy === 'price_asc') {
                $query->orderBy('wastes.price', 'asc');
            } elseif ($sortBy === 'rating_desc') {
                $query->orderBy('average_rating', 'desc');
            } elseif ($sortBy === 'sold_desc') {
                $query->orderBy('wastes.sold_count', 'desc');
            } else {
                $query->latest('wastes.created_at');
            }

            // --- 6. Eksekusi & Respon ---
            $results = $query->paginate(20)->appends($request->query());

            return response()->json($results);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Input tidak valid.', 'errors' => $e->errors()], 422);
        } catch (Throwable $th) {
            report($th);
            return response()->json(['message' => 'Terjadi kesalahan pada server. Silakan cek log untuk detail.', 'error' => $th->getMessage()], 500);
        }
    }
}