<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan baru untuk sebuah transaksi.
     * Pengguna hanya bisa memberi ulasan untuk transaksi mereka sendiri yang sudah 'delivered'.
     * POST /api/transactions/{transaction}/reviews
     */
    public function store(Request $request, Transaction $transaction): JsonResponse
    {
        $user = Auth::user();

        // --- Otorisasi dan Validasi Kondisi ---

        // 1. Pastikan pengguna yang login adalah pembeli dari transaksi ini.
        if ($user->id !== $transaction->buyer_id) {
            return response()->json(['message' => 'Anda tidak diizinkan memberi ulasan untuk transaksi ini.'], 403);
        }

        // 2. Pastikan status transaksi sudah 'delivered'.
        if ($transaction->status !== Transaction::STATUS_DELIVERED) {
            return response()->json(['message' => 'Anda hanya bisa memberi ulasan untuk transaksi yang sudah selesai (delivered).'], 422);
        }

        // 3. Pastikan transaksi ini belum pernah direview sebelumnya.
        if ($transaction->review()->exists()) {
            return response()->json(['message' => 'Anda sudah pernah memberikan ulasan untuk transaksi ini.'], 422);
        }

        // --- Validasi Input ---
        $validatedData = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            // Buat review baru yang terhubung langsung dengan transaksi
            $review = $transaction->review()->create([
                'rating' => $validatedData['rating'],
                'comment' => $validatedData['comment'],
                // transaction_id akan diisi otomatis karena relasi
            ]);

            // Opsional: Anda bisa menambahkan Eco Points karena telah memberikan review
            // $user->increment('eco_points', 5); // Contoh tambah 5 poin
            // EcoPointLog::create([...]);

            return response()->json([
                'message' => 'Ulasan Anda berhasil dikirim!',
                'review' => $review
            ], 201);
        } catch (\Exception $e) {
            Log::error("Gagal menyimpan ulasan untuk transaksi {$transaction->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan ulasan.', 'error' => $e->getMessage()], 500);
        }
    }
}
