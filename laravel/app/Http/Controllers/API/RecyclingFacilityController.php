<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RecyclingFacility;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class RecyclingFacilityController extends Controller
{
    /**
     * Menampilkan daftar fasilitas daur ulang.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'required_with:latitude|sometimes|numeric|between:-180,180',
        ]);

        // Muat juga relasi acceptedCategories
        $query = RecyclingFacility::query()->with(['owner:id,name', 'acceptedCategories:id,name']);

        if ($request->has(['latitude', 'longitude'])) {
            $userLocation = Point::makeGeodetic((float)$request->latitude, (float)$request->longitude);
            $query->selectRaw('*, ST_Distance(location, ?::geography) / 1000 AS distance_km', [$userLocation])
                ->orderBy('distance_km');
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
            'image' => ['nullable', File::image()->max(2048)], // Gambar opsional
            'accepted_categories' => 'required|array', // Wajib ada array kategori
            'accepted_categories.*' => 'required|integer|exists:categories,id', // Setiap item harus ID kategori yang valid
        ]);

        try {
            DB::beginTransaction();

            $facilityData = $request->except(['image', 'latitude', 'longitude', 'accepted_categories']);
            $facilityData['location'] = Point::makeGeodetic((float)$validatedData['latitude'], (float)$validatedData['longitude']);

            if ($request->hasFile('image')) {
                $facilityData['image_path'] = $request->file('image')->store('facility-images', 'public');
            }

            $facility = RecyclingFacility::create($facilityData);
            $facility->acceptedCategories()->sync($validatedData['accepted_categories']);

            DB::commit();

            return response()->json(['message' => 'Fasilitas berhasil dibuat.', 'facility' => $facility->load('acceptedCategories')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal membuat fasilitas: " . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat fasilitas.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail satu fasilitas.
     */
    public function show(RecyclingFacility $recyclingFacility): JsonResponse
    {
        return response()->json($recyclingFacility->load(['owner:id,name', 'acceptedCategories:id,name']));
    }

    /**
     * Memperbarui data fasilitas. Hanya untuk Admin.
     */
    public function update(Request $request, RecyclingFacility $recyclingFacility): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Hanya admin yang dapat mengubah fasilitas.'], 403);
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
            'image' => ['nullable', File::image()->max(2048)], // Gambar opsional
            'accepted_categories' => 'required|array', // Wajib ada array kategori
            'accepted_categories.*' => 'required|integer|exists:categories,id', // Setiap item harus ID kategori yang valid
        ]);

        try {
            DB::beginTransaction();
            $updateData = $request->except(['image', 'latitude', 'longitude', 'accepted_categories']);

            if ($request->has(['latitude', 'longitude'])) {
                $updateData['location'] = Point::makeGeodetic((float)$validatedData['latitude'], (float)$validatedData['longitude']);
            }

            if ($request->hasFile('image')) {
                if ($recyclingFacility->image_path) {
                    Storage::disk('public')->delete($recyclingFacility->image_path);
                }
                $updateData['image_path'] = $request->file('image')->store('facility-images', 'public');
            }

            $recyclingFacility->update($updateData);

            if ($request->has('accepted_categories')) {
                $recyclingFacility->acceptedCategories()->sync($validatedData['accepted_categories']);
            }

            DB::commit();

            return response()->json(['message' => 'Fasilitas berhasil diperbarui.', 'facility' => $recyclingFacility->fresh()->load('acceptedCategories')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update fasilitas {$recyclingFacility->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui fasilitas.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus fasilitas. Hanya untuk Admin.
     */
    public function destroy(RecyclingFacility $recyclingFacility): JsonResponse
    {
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            return response()->json(['message' => 'Hanya admin yang dapat menghapus fasilitas.'], 403);
        }

        // Hapus juga gambar dari storage
        if ($recyclingFacility->image_path) {
            Storage::disk('public')->delete($recyclingFacility->image_path);
        }
        $recyclingFacility->delete(); // record di tabel pivot akan terhapus otomatis

        return response()->json(['message' => 'Fasilitas berhasil dihapus.'], 200);
    }
}
