<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController; // Sesuaikan namespace jika Anda taruh di subfolder API
use App\Http\Controllers\LogisticsController;   // Sesuaikan namespace
use App\Http\Controllers\API\WasteSearchController; // Sesuaikan namespace jika perlu

use App\Http\Controllers\API\StoreController; // Sesuaikan namespace jika perlu

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Endpoint untuk login/autentikasi (Anda mungkin sudah punya atau perlu membuatnya)
// Contoh: 

// Endpoint untuk pencarian limbah (publik)

Route::middleware('auth:sanctum')->group(function () {
    // Endpoint untuk mendapatkan data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles'); // Contoh memuat peran user
    });


    Route::get('/wastes/search', [WasteSearchController::class, 'search'])
        ->name('api.wastes.search');

    
    Route::patch('/logistics/{logistics}/location', [LogisticsController::class, 'updateLocation'])
    ->name('api.logistics.updateLocation');
    Route::get('/logistics/{logistics}/status', [LogisticsController::class, 'getLogisticsStatus'])
    ->name('api.logistics.getStatus');

    // Endpoint untuk Store (toko)
    Route::apiResource('stores', StoreController::class);


    // Endpoint untuk Transaksi (sesuaikan dengan nama rute yang Anda inginkan)
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('api.transactions.show');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('api.transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('api.transactions.store');
    Route::patch('/transactions/{transaction}/confirm-by-seller', [TransactionController::class, 'confirmBySeller'])->name('api.transactions.confirm_by_seller');
    Route::patch('/transactions/{transaction}/pickup-by-distributor', [TransactionController::class, 'markAsPickedUpByDistributor'])->name('api.transactions.pickup_by_distributor');
    Route::patch('/transactions/{transaction}/deliver-by-distributor', [TransactionController::class, 'markAsDeliveredByDistributor'])->name('api.transactions.deliver_by_distributor');
    Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('api.transactions.cancel');

    // Endpoint untuk Logistik
    // Route::get('/dashboard/logistics/{logistics}/update-location', [LogisticsController::class, 'showUpdateLocationForm']) // Pastikan pengguna sudah login
    //     ->name('api.logistics.updateLocationForm'); // Nama rute untuk menampilkan form




    // Endpoint lain yang memerlukan autentikasi
    // Misalnya, untuk StoreController jika ada API untuk itu
    // Route::apiResource('stores', \App\Http\Controllers\API\StoreController::class);
});

// Route::middleware('auth:sanctum')->group(function () {
//     Route::patch('/logistics/{logistics}/location', [LogisticsController::class, 'updateLocation'])
//         ->name('api.logistics.updateLocation');
// });

// Route::patch('/logistics/{logistics}/location', [LogisticsController::class, 'updateLocation'])
//     ->middleware(['auth:sanctum'])
//     ->name('api.logistics.updateLocation');

