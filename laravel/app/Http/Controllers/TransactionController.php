<?php

namespace App\Http\Controllers;


// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// use App\Models\Transaction;
// use App\Models\WasteVariant;
// use App\Models\Waste;
// use App\Models\User;
// use App\Models\EcoPointLog;
// use App\Models\Logistics;   // Pastikan model ini ada
// use App\Models\Store;       // Pastikan model ini ada
// // use App\Models\Certificate; // Jika diperlukan
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Carbon\Carbon;
// use App\Notifications\TransactionStatusUpdated; // Anda bisa membuat notifikasi


// Model yang digunakan oleh controller ini
use App\Models\Transaction;
use App\Models\WasteVariant;
use App\Models\Waste;
use App\Models\User;
use App\Models\EcoPointLog;
use App\Models\Logistics;
use App\Models\Store;

// Facades dan Class lain yang digunakan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Clickbar\Magellan\Data\Geometries\Point; // Import Point

class TransactionController extends Controller
{
    /**
     * Buyer membuat transaksi baru.
     * Status awal: 'pending'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'waste_variant_id' => 'required|exists:waste_variants,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cod,bank_transfer_bca,dll', // Sesuaikan
            // Tidak perlu validasi address_id atau field delivery_* dari request
            // karena kita akan mengambil dari profil buyer yang login
        ]);

        $buyer = Auth::user();
        if (!$buyer || !$buyer->hasRole('buyer')) {
            return response()->json(['message' => 'Hanya buyer yang dapat membuat transaksi.'], 403);
        }

        // Pastikan buyer memiliki data lokasi di profilnya
        if (!$buyer->location instanceof Point || is_null($buyer->location->getLatitude()) || is_null($buyer->location->getLongitude())) {
            return response()->json(['message' => 'Harap lengkapi data lokasi (latitude/longitude) di profil Anda untuk alamat pengiriman.'], 422);
        }
        $buyerLocationPoint = $buyer->location; // Ini sudah objek Point

        $wasteVariant = WasteVariant::with('waste.store.user')->findOrFail($request->waste_variant_id);

        if ($wasteVariant->stock < $request->quantity) {
            return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
        }

        $totalPrice = $wasteVariant->price * $request->quantity;
        $store = $wasteVariant->waste->store;
        $seller = $store->user;

        try {
            DB::beginTransaction();

            $transaction = Transaction::create([
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'waste_variant_id' => $wasteVariant->id,
                'store_id' => $store->id,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => $request->payment_method,
                'eco_points_earned' => 0,

                // Salin alamat dari profil buyer ke transaksi
                'delivery_recipient_name' => $buyer->name, // Atau nama penerima jika ada field terpisah di profil user
                'delivery_phone_number' => $buyer->phone_number,
                'delivery_address_detail' => $buyer->address_detail,
                'delivery_village' => $buyer->village,
                'delivery_subdistrict' => $buyer->subdistrict,
                'delivery_city_regency' => $buyer->city_regency,
                'delivery_province' => $buyer->province,
                'delivery_postal_code' => $buyer->postal_code,
                'delivery_location' => $buyerLocationPoint, // Simpan objek Point
                'delivery_notes' => $buyer->address_notes, // Atau dari input request jika ada field catatan pengiriman
            ]);

            $wasteVariant->decrement('stock', $request->quantity);

            DB::commit();
            Log::info("Transaction {$transaction->id} created by Buyer {$buyer->id}. Status: PENDING.");
            // ... (notifikasi)

            return response()->json([
                'message' => 'Transaksi berhasil dibuat dan menunggu konfirmasi.',
                'transaction' => $transaction
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating transaction: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Gagal membuat transaksi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Seller mengkonfirmasi transaksi.
     * Status: 'pending' -> 'confirmed'.
     */
    public function confirmBySeller(Transaction $transaction, Request $request) // Request bisa berisi distributor_id jika seller yg assign
    {
        $seller = Auth::user();
        if (!($transaction->seller_id == $seller->id && $seller->hasRole('seller')) && !$seller->hasRole('admin')) {
            return response()->json(['message' => 'Hanya penjual terkait atau admin yang dapat mengkonfirmasi transaksi ini.'], 403);
        }

        if ($transaction->status !== Transaction::STATUS_PENDING) {
            return response()->json(['message' => 'Hanya transaksi dengan status pending yang bisa dikonfirmasi.'], 422);
        }

        // TODO: Logika verifikasi pembayaran jika bukan COD

        try {
            DB::beginTransaction();
            $transaction->status = Transaction::STATUS_CONFIRMED;

            // Jika seller menugaskan distributor saat konfirmasi
            if ($request->filled('distributor_id')) {
                $request->validate(['distributor_id' => 'required|exists:users,id']); // Pastikan distributor_id adalah user yg valid
                $distributor = User::findOrFail($request->distributor_id);
                if (!$distributor->hasRole('distributor')) {
                    return response()->json(['message' => 'User yang dipilih bukan distributor.'], 422);
                }

                $logistics = Logistics::create([
                    'transaction_id' => $transaction->id,
                    'distributor_id' => $request->distributor_id,
                    'status' => 'scheduled', // Status awal logistik
                ]);
                $transaction->logistics_id = $logistics->id;
            }

            $transaction->save();
            DB::commit();

            Log::info("Transaction {$transaction->id} confirmed by Seller/Admin {$seller->id}. Status: CONFIRMED.");
            // TODO: Notifikasi ke Buyer
            // $transaction->buyer->notify(new TransactionStatusUpdated($transaction, 'Pesanan Dikonfirmasi'));
            // Jika distributor ditugaskan, notifikasi juga ke distributor
            // if ($transaction->logistics_id && $transaction->logistics->distributor) {
            //    $transaction->logistics->distributor->notify(new NewDeliveryTaskNotification($transaction));
            // }


            return response()->json(['message' => 'Transaksi berhasil dikonfirmasi.', 'transaction' => $transaction->load('logistics')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error confirming transaction: {$e->getMessage()}");
            return response()->json(['message' => 'Gagal mengkonfirmasi transaksi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Distributor menandai transaksi sebagai sudah di-pickup.
     * Status: 'confirmed' -> 'picked_up'.
     */
    public function markAsPickedUpByDistributor(Transaction $transaction, Request $request)
    {
        $distributor = Auth::user();
        if (!$distributor->hasRole('distributor')) {
            return response()->json(['message' => 'Hanya distributor yang dapat melakukan aksi ini.'], 403);
        }

        if ($transaction->status !== Transaction::STATUS_CONFIRMED) {
            return response()->json(['message' => 'Transaksi belum dikonfirmasi atau sudah dalam status lain.'], 422);
        }

        try {
            DB::beginTransaction();
            $transaction->status = Transaction::STATUS_PICKED_UP;

            // Jika logistics_id belum ada (misal, distributor "mengklaim" tugas)
            // atau jika distributor yang melakukan aksi ini harus dicatat
            if (!$transaction->logistics_id) {
                $logistics = Logistics::create([
                    'transaction_id' => $transaction->id,
                    'distributor_id' => $distributor->id,
                    'status' => 'in_transit',
                ]);
                $transaction->logistics_id = $logistics->id;
            } elseif ($transaction->logistics && $transaction->logistics->distributor_id == $distributor->id) {
                $transaction->logistics->update(['status' => 'in_transit', 'last_updated_at' => Carbon::now()]);
            } elseif ($transaction->logistics && $transaction->logistics->distributor_id != $distributor->id) {
                DB::rollBack();
                return response()->json(['message' => 'Anda bukan distributor yang ditugaskan untuk transaksi ini.'], 403);
            } else {
                // Kasus aneh, logistics_id ada tapi relasi tidak ketemu
                DB::rollBack();
                return response()->json(['message' => 'Data logistik tidak ditemukan atau tidak valid.'], 422);
            }

            $transaction->save();
            DB::commit();

            Log::info("Transaction {$transaction->id} marked as picked_up by Distributor {$distributor->id}. Status: PICKED_UP.");
            // TODO: Notifikasi ke Buyer & Seller
            // $transaction->buyer->notify(new TransactionStatusUpdated($transaction, 'Pesanan Sudah Diambil oleh Kurir'));
            // $transactiondecr->seller->notify(new TransactionStatusUpdated($transaction, 'Pesanan Sudah Diambil oleh Kurir'));

            return response()->json(['message' => 'Transaksi ditandai sudah diambil.', 'transaction' => $transaction->load('logistics')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error marking transaction as picked_up: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Gagal memperbarui status transaksi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Distributor menandai transaksi sebagai sudah terkirim.
     * Status: 'picked_up' -> 'delivered'.
     */
    public function markAsDeliveredByDistributor(Transaction $transaction)
    {
        $distributor = Auth::user();
        if (!$distributor->hasRole('distributor')) {
            return response()->json(['message' => 'Hanya distributor yang dapat melakukan aksi ini.'], 403);
        }

        // Pastikan distributor yang melakukan aksi adalah yang ditugaskan
        if (!$transaction->logistics || $transaction->logistics->distributor_id !== $distributor->id) {
            return response()->json(['message' => 'Anda bukan distributor yang ditugaskan untuk transaksi ini.'], 403);
        }

        if ($transaction->status !== Transaction::STATUS_PICKED_UP) {
            return response()->json(['message' => 'Transaksi belum dalam status pengiriman (picked_up).'], 422);
        }

        try {
            DB::beginTransaction();

            $transaction->status = Transaction::STATUS_DELIVERED;
            $transaction->completed_at = Carbon::now();

            // 1. Finalisasi Stok
            $wasteVariant = $transaction->wasteVariant; // Relasi sudah di-load jika perlu
            if ($wasteVariant) {
                // $newStock = $wasteVariant->stock - $transaction->quantity;
                // $wasteVariant->stock = max(0, $newStock);
                $wasteVariant->save();

                $parentWaste = $wasteVariant->waste;
                if ($parentWaste) {
                    $parentWaste->increment('sold_count', $transaction->quantity);
                }
            }

            // 2. Berikan Eco Points ke Buyer
            $pointsToEarn = 10 * $transaction->quantity; // Contoh: 10 poin per item
            if ($pointsToEarn > 0) {
                $transaction->eco_points_earned = $pointsToEarn;
                $buyer = $transaction->buyer;
                $buyer->increment('eco_points', $pointsToEarn);

                EcoPointLog::create([
                    'user_id' => $buyer->id,
                    'source_type' => EcoPointLog::SOURCE_TRANSACTION,
                    'reference_id' => $transaction->id,
                    'points' => $pointsToEarn,
                    'points_type' => EcoPointLog::TYPE_PLUS,
                    'description' => "Poin dari penyelesaian transaksi #{$transaction->id}",
                    'created_at' => Carbon::now()
                ]);
            }
            $transaction->save();

            // 3. Update status logistik
            $transaction->logistics->update(['status' => 'delivered', 'last_updated_at' => Carbon::now()]);

            // 4. Buat Sertifikat (jika ada logika ini)
            // ...

            DB::commit();
            Log::info("Transaction {$transaction->id} marked as delivered by Distributor {$distributor->id}. Status: DELIVERED.");
            // TODO: Notifikasi ke Buyer & Seller
            // $transaction->buyer->notify(new TransactionStatusUpdated($transaction, 'Pesanan Telah Diterima'));
            // $transaction->seller->notify(new TransactionStatusUpdated($transaction, 'Pesanan Telah Diterima oleh Pembeli'));


            return response()->json(['message' => 'Transaksi berhasil diselesaikan.', 'transaction' => $transaction->load(['logistics', 'certificate', 'ecoPointLogs'])]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error marking transaction as delivered: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Gagal menyelesaikan transaksi.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Membatalkan transaksi.
     * Bisa dilakukan oleh Buyer jika status 'pending', atau oleh Seller/Admin.
     */
    public function cancel(Transaction $transaction, Request $request)
    {
        $user = Auth::user();
        $canCancel = false;
        $reason = $request->input('reason', 'Dibatalkan oleh pengguna.'); // Alasan pembatalan

        // Buyer bisa cancel jika status masih pending
        if ($user->id == $transaction->buyer_id && $transaction->status == Transaction::STATUS_PENDING) {
            $canCancel = true;
        }
        // Seller bisa cancel jika status pending atau confirmed (sebelum picked_up)
        elseif ($user->id == $transaction->seller_id && in_array($transaction->status, [Transaction::STATUS_PENDING, Transaction::STATUS_CONFIRMED])) {
            $canCancel = true;
        }
        // Admin bisa cancel kapan saja sebelum delivered
        elseif ($user->hasRole('admin') && !in_array($transaction->status, [Transaction::STATUS_DELIVERED, Transaction::STATUS_CANCELLED])) {
            $canCancel = true;
        }

        if (!$canCancel) {
            return response()->json(['message' => 'Anda tidak diizinkan membatalkan transaksi ini pada status saat ini.'], 403);
        }

        if (in_array($transaction->status, [Transaction::STATUS_DELIVERED, Transaction::STATUS_CANCELLED])) {
            return response()->json(['message' => 'Transaksi yang sudah selesai atau sudah dibatalkan tidak bisa dibatalkan lagi.'], 422);
        }

        try {
            DB::beginTransaction();

            $originalStatus = $transaction->status;
            $transaction->status = Transaction::STATUS_CANCELLED;
            $transaction->completed_at = Carbon::now(); // Atau gunakan kolom cancelled_at
            // Anda bisa menambahkan kolom 'cancellation_reason' di tabel transactions
            // $transaction->cancellation_reason = $reason;
            $transaction->save();

            // Kembalikan stok jika transaksi sebelumnya sudah mengurangi/memesan stok
            // (Ini perlu disesuaikan dengan logika kapan stok awal dikurangi)
            if (in_array($originalStatus, [Transaction::STATUS_PENDING, Transaction::STATUS_CONFIRMED, Transaction::STATUS_PICKED_UP])) {
                $wasteVariant = $transaction->wasteVariant;
                if ($wasteVariant) {
                    $wasteVariant->increment('stock', $transaction->quantity);
                }
            }

            // Update status logistik (jika ada)
            if ($transaction->logistics) {
                $transaction->logistics->update(['status' => 'cancelled', 'last_updated_at' => Carbon::now()]);
            }

            // TODO: Proses refund jika pembayaran sudah dilakukan dan dikonfirmasi

            DB::commit();
            Log::info("Transaction {$transaction->id} cancelled by User {$user->id}. Reason: {$reason}");
            // TODO: Notifikasi
            // $transaction->buyer->notify(new TransactionStatusUpdated($transaction, "Pesanan Dibatalkan. Alasan: {$reason}"));
            // $transaction->seller->notify(new TransactionStatusUpdated($transaction, "Pesanan Dibatalkan oleh {$user->name}. Alasan: {$reason}"));

            return response()->json(['message' => 'Transaksi berhasil dibatalkan.', 'transaction' => $transaction->load('logistics')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error cancelling transaction: {$e->getMessage()} at {$e->getFile()}:{$e->getLine()}");
            return response()->json(['message' => 'Gagal membatalkan transaksi.', 'error' => $e->getMessage()], 500);
        }
    }

    // Method untuk menampilkan detail transaksi
    public function show(Transaction $transaction)
    {
        $user = Auth::user();
        // Otorisasi: Buyer, Seller, Distributor terkait, atau Admin bisa melihat
        $canView = false;
        if ($user->id === $transaction->buyer_id || $user->id === $transaction->seller_id || $user->hasRole('admin')) {
            $canView = true;
        } elseif ($transaction->logistics && $transaction->logistics->distributor_id === $user->id && $user->hasRole('distributor')) {
            $canView = true;
        }

        if (!$canView) {
            return response()->json(['message' => 'Tidak diizinkan melihat transaksi ini.'], 403);
        }
        return response()->json($transaction->load(['buyer', 'seller', 'wasteVariant.waste.category', 'store', 'logistics.distributorUser', 'certificate', 'ecoPointLogs']));
        // Asumsi di model Logistics ada relasi distributorUser ke User model
    }

    // Method untuk menampilkan daftar transaksi (misalnya untuk user tertentu)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::query();

        if ($user->hasRole('admin')) {
            // Admin bisa lihat semua
        } elseif ($user->hasRole('seller')) {
            $query->where('seller_id', $user->id);
        } elseif ($user->hasRole('buyer')) {
            $query->where('buyer_id', $user->id);
        } elseif ($user->hasRole('distributor')) {
            $query->whereHas('logistics', function ($q) use ($user) {
                $q->where('distributor_id', $user->id);
            });
        } else {
            return response()->json(['message' => 'Tidak ada transaksi untuk ditampilkan.'], 200); // Atau 403
        }

        $transactions = $query->with(['buyer:id,name', 'seller:id,name', 'wasteVariant:id,volume_in_grams', 'store:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($transactions);
    }
}
