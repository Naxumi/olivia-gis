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
    /**
     * Mencari produk limbah dengan berbagai filter dan opsi urutan.
     * GET /api/wastes/search
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // --- 1. Validasi Input ---
            $validated = $request->validate([
                'q' => 'nullable|string|max:255',
                'category' => 'nullable|string|exists:categories,name',
                'status' => 'nullable|string|in:available,sold,expired',
                'sort_by' => 'nullable|string|in:nearby,price_asc,rating_desc,sold_desc',
                'latitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-90,90',
                'longitude' => 'required_if:sort_by,nearby|nullable|numeric|between:-180,180',
            ]);

            // --- 2. Query Dasar dengan Join ---
            $query = Waste::query()
                ->join('stores', 'wastes.store_id', '=', 'stores.id')
                ->join('categories', 'wastes.category_id', '=', 'categories.id')
                // --- PERBAIKAN JOIN UNTUK REVIEW ---
                // Join ke transactions dulu, baru ke reviews dari transactions
                ->leftJoin('transactions', 'stores.id', '=', 'transactions.store_id')
                ->leftJoin('reviews', 'transactions.id', '=', 'reviews.transaction_id')
                ->whereNotNull('stores.location');

            // --- 3. Kolom Pilihan & Agregasi ---
            $selectColumns = [
                'wastes.id as waste_id',
                'wastes.name as waste_name',
                'wastes.stock',
                'wastes.status',
                'wastes.price',
                'wastes.sold_count',
                'wastes.created_at',
                'stores.id as store_id',
                'stores.name as store_name',
                'stores.location'
            ];

            $groupByColumns = [
                'wastes.id',
                'wastes.name',
                'wastes.stock',
                'wastes.status',
                'wastes.price',
                'wastes.sold_count',
                'wastes.created_at',
                'stores.id',
                'stores.name',
                'stores.location'
            ];

            $query->select($selectColumns)
                ->selectRaw('COALESCE(AVG(reviews.rating), 0) as average_rating')
                ->groupBy($groupByColumns);

            // --- 4. Menerapkan Filter ---
            if (!empty($validated['q'])) {
                $query->where('wastes.name', 'ILIKE', '%' . $validated['q'] . '%');
            }
            if (!empty($validated['category'])) {
                $query->where('categories.name', $validated['category']);
            }
            if (!empty($validated['status'])) {
                $query->where('wastes.status', $validated['status']);
            }

            // --- 5. Menerapkan Pengurutan ---
            $sortBy = $validated['sort_by'] ?? null;
            if ($sortBy === 'nearby' && isset($validated['latitude'], $validated['longitude'])) {
                $userLocation = Point::makeGeodetic((float)$validated['latitude'], (float)$validated['longitude']);
                $query->selectRaw('ST_Distance(stores.location, ?::geography) / 1000 AS distance_km', [$userLocation])
                    ->orderBy('distance_km', 'asc');
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
            if (config('app.debug')) {
                return response()->json(['message' => 'Terjadi kesalahan pada server.', 'error' => $th->getMessage()], 500);
            }
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
