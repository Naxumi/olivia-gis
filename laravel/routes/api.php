<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\API\WasteSearchController;

use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\API\RecyclingFacilityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan rute API untuk aplikasi Anda. Semua rute
| yang didefinisikan di sini akan otomatis memiliki prefix URL '/api/'.
|
*/

//======================================================================
// RUTE PUBLIK (Tidak Perlu Login)
//======================================================================

// Endpoint untuk pencarian limbah. Publik agar semua orang bisa melihat marketplace.
Route::get('/wastes/search', [WasteSearchController::class, 'search'])->name('api.wastes.search');

// Rute Publik untuk melihat daftar dan detail fasilitas
Route::get('/recycling-facilities', [RecyclingFacilityController::class, 'index'])->name('api.recycling-facilities.index');
Route::get('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'show'])->name('api.recycling-facilities.show');


//======================================================================
// RUTE AUTENTIKASI
//======================================================================

// Rute untuk login dan register tidak memerlukan middleware 'auth:sanctum'.
// Route::post('/register', [AuthController::class, 'register'])->name('api.register');
// Route::post('/login', [AuthController::class, 'login'])->name('api.login');


//======================================================================
// RUTE TERPROTEKSI (Wajib Login dengan Token Sanctum)
//======================================================================

// Route::middleware('auth:sanctum')->group(function () {

    // --- Endpoint Pengguna ---
    // Mengambil data pengguna yang sedang terautentikasi.
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'distributorProfile'); // Muat info tambahan
    });

    // Endpoint untuk logout.
    // Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');


    // --- Endpoint Toko (Stores) ---
    // Menyediakan fungsi CRUD (Create, Read, Update, Delete) untuk toko.
    // Dikelola oleh pengguna dengan peran 'seller' atau 'admin'.
//    Route::apiResource('stores', StoreController::class);


    // --- Endpoint Transaksi (Transactions) ---
    // Mengelola seluruh alur transaksi dari pemesanan hingga selesai.
    Route::controller(TransactionController::class)->prefix('transactions')->as('api.transactions.')->group(function () {
        Route::get('/', 'index')->name('index'); // Daftar transaksi (sesuai peran)
        Route::post('/', 'store')->name('store'); // Membuat transaksi baru (oleh Buyer)
        Route::get('/{transaction}', 'show')->name('show'); // Melihat detail transaksi

        // Rute untuk mengubah status transaksi
        Route::patch('/{transaction}/confirm-by-seller', 'confirmBySeller')->name('confirm_by_seller'); // Oleh Seller/Admin
        Route::patch('/{transaction}/pickup-by-distributor', 'markAsPickedUpByDistributor')->name('pickup_by_distributor'); // Oleh Distributor
        Route::patch('/{transaction}/deliver-by-distributor', 'markAsDeliveredByDistributor')->name('deliver_by_distributor'); // Oleh Distributor
        Route::patch('/{transaction}/cancel', 'cancel')->name('cancel'); // Oleh Buyer/Seller/Admin
    });


    // --- Endpoint Logistik (Logistics) ---
    // Mengelola fitur live tracking untuk pengiriman.
    Route::controller(LogisticsController::class)->prefix('logistics')->as('api.logistics.')->group(function () {
        // Endpoint untuk distributor mengirimkan update lokasi mereka.
        Route::patch('/{logistics}/location', 'updateLocation')->name('updateLocation');

        // Endpoint untuk buyer/seller melihat status dan posisi terakhir pengiriman.
        Route::get('/{logistics}/status', 'getLogisticsStatus')->name('getStatus');
    });

    // --- Endpoint Waste (Limbah) ---
    // Mengelola data limbah yang dimiliki oleh toko.
    Route::get('/wastes/{waste}', [WasteController::class, 'show'])->name('api.wastes.show');
    Route::post('/wastes/{waste}', [WasteController::class, 'update'])->name('api.wastes.update'); // Gunakan POST dengan _method=PATCH untuk upload file
    Route::delete('/wastes/{waste}', [WasteController::class, 'destroy'])->name('api.wastes.destroy');

    // Rute Terproteksi untuk Admin mengelola fasilitas
    Route::middleware(['role:admin'])->group(function () {
        // Anda bisa menggunakan pengecekan peran di middleware seperti 'role:admin' jika sudah di-setup
        // dengan Spatie, atau biarkan pengecekan di dalam controller seperti contoh di atas.
        Route::post('/recycling-facilities', [RecyclingFacilityController::class, 'store'])->name('api.recycling-facilities.store');
        Route::put('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'update'])->name('api.recycling-facilities.update'); // PUT untuk mengganti semua field
        Route::patch('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'update'])->name('api.recycling-facilities.update-partial'); // PATCH untuk update sebagian
        Route::delete('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'destroy'])->name('api.recycling-facilities.destroy');
    });
// });
