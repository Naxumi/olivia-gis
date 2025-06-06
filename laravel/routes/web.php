<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\WasteVariantController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\WasteSearchController;
use App\Http\Controllers\API\RecyclingFacilityController;
use App\Http\Controllers\TransactionController;
use App\Models\Waste;
use App\Models\Transaction;
use App\Http\Controllers\LogisticsController; // Kita akan buat controller ini

use App\Http\Controllers\Auth\AuthenticatedSessionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan rute web untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dalam grup yang berisi middleware 'web'
| (sesi, proteksi CSRF, dll.). Cocok untuk aplikasi web tradisional.
|
*/

//======================================================================
// RUTE PUBLIK (Dapat Diakses oleh Siapa Saja/Tamu)
//======================================================================

// Halaman utama / landing page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Halaman utama marketplace untuk melihat semua produk limbah
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');

// Rute untuk menampilkan peta publik
Route::get('/peta-interaktif', function () {
    // Anda bisa menambahkan logika untuk mengambil data lokasi toko awal di sini
    return view('map-detail');
})->name('map.interactive');


//======================================================================
// RUTE AUTENTIKASI (Login, Register, dll.)
//======================================================================
// File auth.php (dibuat oleh Laravel Breeze/UI) biasanya sudah menangani
// rute untuk login, register, forgot password, dll.
// Pastikan baris ini ada di bagian akhir file.
require __DIR__ . '/auth.php';


//======================================================================
// RUTE TERPROTEKSI (Wajib Login)
//======================================================================

Route::middleware(['auth', 'verified'])->group(function () { // 'verified' untuk memastikan email sudah terverifikasi

    // --- Dashboard ---
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- Profil Pengguna ---
    Route::controller(ProfileController::class)->prefix('profile')->as('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // Rute bersarang untuk Wastes yang dimiliki oleh Store.
    // URL akan menjadi: /stores/{store}/wastes, /stores/{store}/wastes/{waste}, dll.
    Route::resource('stores.wastes', WasteController::class)->scoped();
    Route::apiResource(name: 'stores', StoreController::class);


    // Rute bersarang untuk Waste Variants yang dimiliki oleh Waste.
    // URL akan menjadi: /wastes/{waste}/variants, /wastes/{waste}/variants/{variant}, dll.
    Route::resource('wastes.variants', WasteVariantController::class)->scoped();

    // --- Halaman Debug/Testing (Hanya untuk Development) ---
    // Sebaiknya diproteksi lebih lanjut atau dihapus saat produksi.
    Route::get('/test-peta', function () {
        return view('test-map');
    })->name('test.map');
});


Route::get('/wastes/search', [WasteSearchController::class, 'search'])->name('api.wastes.search');

// Rute Publik untuk melihat daftar dan detail fasilitas
Route::get('/recycling-facilities', [RecyclingFacilityController::class, 'index'])->name('api.recycling-facilities.index');
Route::get('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'show'])->name('api.recycling-facilities.show');

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

 Route::controller(LogisticsController::class)->prefix('logistics')->as('api.logistics.')->group(function () {
        // Endpoint untuk distributor mengirimkan update lokasi mereka.
        Route::patch('/{logistics}/location', 'updateLocation')->name('updateLocation');

        // Endpoint untuk buyer/seller melihat status dan posisi terakhir pengiriman.
        Route::get('/{logistics}/status', 'getLogisticsStatus')->name('getStatus');
    });

 Route::get('/wastes/{waste}', [WasteController::class, 'show'])->name('api.wastes.show');
    Route::post('/wastes/{waste}', [WasteController::class, 'update'])->name('api.wastes.update'); // Gunakan POST dengan _method=PATCH untuk upload file
    Route::delete('/wastes/{waste}', [WasteController::class, 'destroy'])->name('api.wastes.destroy');

Route::middleware(['role:admin'])->group(function () {
        // Anda bisa menggunakan pengecekan peran di middleware seperti 'role:admin' jika sudah di-setup
        // dengan Spatie, atau biarkan pengecekan di dalam controller seperti contoh di atas.
        Route::post('/recycling-facilities', [RecyclingFacilityController::class, 'store'])->name('api.recycling-facilities.store');
        Route::put('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'update'])->name('api.recycling-facilities.update'); // PUT untuk mengganti semua field
        Route::patch('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'update'])->name('api.recycling-facilities.update-partial'); // PATCH untuk update sebagian
        Route::delete('/recycling-facilities/{recyclingFacility}', [RecyclingFacilityController::class, 'destroy'])->name('api.recycling-facilities.destroy');
    });