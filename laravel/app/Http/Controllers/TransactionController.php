<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\WasteVariant;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Pastikan hanya buyer yang bisa membuat transaksi
        // $this->middleware('role:buyer')->only(['create', 'store']);
    }

    public function store(Request $request)
    {
        // Pastikan user adalah buyer
        if (!Auth::user()->roles()->where('name', 'buyer')->exists()) {
            abort(403, 'Hanya buyer yang dapat melakukan transaksi.');
        }

        $validated = $request->validate([
            'waste_variant_id' => 'required|exists:waste_variants,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string', // Anda perlu daftar payment method
        ]);

        $variant = WasteVariant::with('waste.store')->findOrFail($validated['waste_variant_id']);
        $store = $variant->waste->store; // Ambil store dari relasi

        if ($variant->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Sisa stok: ' . $variant->stock])->withInput();
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'buyer_id' => Auth::id(),
                'seller_id' => $store->user_id, // Seller adalah pemilik toko
                'waste_variant_id' => $variant->id,
                'store_id' => $store->id,
                'quantity' => $validated['quantity'],
                'total_price' => $variant->price * $validated['quantity'],
                'status' => 'pending', // Status awal
                'payment_method' => $validated['payment_method'],
                'eco_points_earned' => 0, // Akan diupdate saat selesai
            ]);

            // Kurangi stok
            $variant->decrement('stock', $validated['quantity']);

            DB::commit();

            // Redirect ke halaman detail transaksi atau pembayaran
            return redirect()->route('transactions.show', $transaction)->with('success', 'Pesanan berhasil dibuat, menunggu pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Transaction creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal membuat transaksi, silakan coba lagi.'])->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        // Pastikan user yang login adalah buyer atau seller dari transaksi ini
        $this->authorize('view', $transaction); // Gunakan Policy
        return view('transactions.show', compact('transaction'));
    }
    // ... method lainnya
}
