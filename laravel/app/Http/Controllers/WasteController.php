<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\WasteImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class WasteController extends Controller
{
    /**
     * Menampilkan daftar limbah dari sebuah toko.
     * GET /api/stores/{store}/wastes
     */
    public function index(Store $store): JsonResponse
    {
        // Otorisasi: Siapa saja bisa melihat limbah di sebuah toko (untuk marketplace)
        $wastes = $store->wastes()->with('images', 'category')->latest()->paginate(10);
        return response()->json($wastes);
    }

    /**
     * Menyimpan data limbah baru beserta gambarnya.
     * POST /api/stores/{store}/wastes
     */
    public function store(Request $request, Store $store): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $store->user_id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak diizinkan menambah limbah untuk toko ini.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:available,sold,expired',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:5', // Izinkan maks 5 gambar sekaligus
            'images.*' => ['required', File::image()->max(2048)], // Setiap file harus gambar, maks 2MB
        ]);

        try {
            DB::beginTransaction();

            $waste = $store->wastes()->create([
                'name' => $validatedData['name'],
                'category_id' => $validatedData['category_id'],
                'stock' => $validatedData['stock'],
                'status' => $validatedData['status'],
                'price' => $validatedData['price'],
                'description' => $validatedData['description'],
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    $path = $imageFile->store('waste-images', 'public');
                    $waste->images()->create(['path' => $path]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Data limbah berhasil ditambahkan!',
                'waste' => $waste->load('images')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menyimpan limbah: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan data limbah.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail satu data limbah.
     * GET /api/wastes/{waste}
     */
    public function show(Waste $waste): JsonResponse
    {
        return response()->json($waste->load('images', 'category', 'store.user:id,name'));
    }

    /**
     * Memperbarui data limbah.
     * CATATAN: Untuk update file, request dari frontend harus POST dengan _method=PATCH
     */
    public function update(Request $request, Waste $waste): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $waste->store->user_id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak diizinkan mengubah data ini.'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:available,sold,expired',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $waste->update($validatedData);
            return response()->json([
                'message' => 'Data limbah berhasil diperbarui!',
                'waste' => $waste->fresh()->load('images')
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal update limbah {$waste->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui data limbah.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus data limbah beserta semua gambarnya.
     * DELETE /api/wastes/{waste}
     */
    public function destroy(Waste $waste): JsonResponse
    {
        $user = Auth::user();
        if ($user->id !== $waste->store->user_id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak diizinkan menghapus data ini.'], 403);
        }

        try {
            DB::beginTransaction();
            // Hapus semua file gambar dari storage
            foreach ($waste->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
            // Hapus record dari database (record di waste_images akan terhapus otomatis karena onDelete('cascade'))
            $waste->delete();
            DB::commit();
            return response()->json(['message' => 'Data limbah dan semua gambarnya berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus limbah {$waste->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus data limbah.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus satu gambar spesifik dari sebuah data limbah.
     * DELETE /api/waste-images/{waste_image}
     */
    public function destroyImage(WasteImage $wasteImage): JsonResponse
    {
        $user = Auth::user();
        $waste = $wasteImage->waste; // Dapatkan parent waste dari gambar

        if ($user->id !== $waste->store->user_id && !$user->hasRole('admin')) {
            return response()->json(['message' => 'Anda tidak diizinkan menghapus gambar ini.'], 403);
        }

        try {
            // Hapus file dari storage
            Storage::disk('public')->delete($wasteImage->path);
            // Hapus record dari database
            $wasteImage->delete();
            return response()->json(['message' => 'Gambar berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus gambar {$wasteImage->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menghapus gambar.', 'error' => $e->getMessage()], 500);
        }
    }
}
