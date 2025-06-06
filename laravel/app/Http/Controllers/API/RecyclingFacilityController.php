<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RecyclingFacility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Validation\Rule;

class RecyclingFacilityController extends Controller
{
    /**
     * Menampilkan daftar fasilitas daur ulang, bisa diurutkan berdasarkan jarak.
     * GET /api/recycling-facilities
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|sometimes|numeric|between:-180,180',
        ]);

        $query = RecyclingFacility::query()->with('owner:id,name');

        // Jika ada parameter latitude dan longitude, hitung dan urutkan berdasarkan jarak
        if ($request->has(['latitude', 'longitude'])) {
            $userLocation = Point::makeGeodetic(
                latitude: (float)$request->latitude,
                longitude: (float)$request->longitude
            );

            $query->selectRaw(
                '*, ST_Distance(location, ?::geography) / 1000 AS distance_km',
                [$userLocation]
            )->orderBy('distance_km');
        }

        $facilities = $query->paginate(15);

        return response()->json($facilities);
    }

    /**
     * Menyimpan fasilitas daur ulang baru. Hanya untuk Admin.
     * POST /api/recycling-facilities
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Hanya admin yang dapat menambah fasilitas.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['rvm', 'waste_bank'])],
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'operational_hours' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'owner_id' => 'required|exists:users,id',
        ]);

        $locationPoint = Point::makeGeodetic((float)$validatedData['latitude'], (float)$validatedData['longitude']);

        $facility = RecyclingFacility::create([
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'address' => $validatedData['address'],
            'location' => $locationPoint,
            'operational_hours' => $validatedData['operational_hours'],
            'contact_person' => $validatedData['contact_person'],
            'contact_phone' => $validatedData['contact_phone'],
            'owner_id' => $validatedData['owner_id'],
        ]);

        return response()->json(['message' => 'Fasilitas berhasil dibuat.', 'facility' => $facility], 201);
    }

    /**
     * Menampilkan detail satu fasilitas.
     * GET /api/recycling-facilities/{recycling_facility}
     */
    public function show(RecyclingFacility $recyclingFacility): JsonResponse
    {
        return response()->json($recyclingFacility->load('owner:id,name'));
    }

    /**
     * Memperbarui data fasilitas. Hanya untuk Admin.
     * PUT/PATCH /api/recycling-facilities/{recycling_facility}
     */
    public function update(Request $request, RecyclingFacility $recyclingFacility): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Hanya admin yang dapat mengubah fasilitas.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => ['sometimes', 'required', Rule::in(['rvm', 'waste_bank'])],
            'address' => 'sometimes|required|string',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|sometimes|numeric|between:-180,180',
            'operational_hours' => 'sometimes|required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'owner_id' => 'sometimes|required|exists:users,id',
        ]);

        if (isset($validatedData['latitude']) && isset($validatedData['longitude'])) {
            $validatedData['location'] = Point::makeGeodetic((float)$validatedData['latitude'], (float)$validatedData['longitude']);
            unset($validatedData['latitude'], $validatedData['longitude']);
        }

        $recyclingFacility->update($validatedData);

        return response()->json(['message' => 'Fasilitas berhasil diperbarui.', 'facility' => $recyclingFacility]);
    }

    /**
     * Menghapus fasilitas. Hanya untuk Admin.
     * DELETE /api/recycling-facilities/{recycling_facility}
     */
    public function destroy(RecyclingFacility $recyclingFacility): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus fasilitas.'], 403);
        }

        $recyclingFacility->delete();

        return response()->json(['message' => 'Fasilitas berhasil dihapus.'], 200);
    }
}