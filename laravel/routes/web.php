<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\WasteVariantController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\TransactionController;
use App\Models\Waste;
use App\Models\Transaction;
use App\Http\Controllers\LogisticsController; // Kita akan buat controller ini

use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Rute untuk MENAMPILKAN form update lokasi
Route::get('/dashboard/logistics/{logistics}/update-location', [LogisticsController::class, 'showUpdateLocationForm'])
    ->middleware(['auth']) // Pastikan pengguna sudah login
    ->name('logistics.updateLocationForm'); // Nama rute untuk menampilkan form

// Rute untuk MEMPROSES SUBMIT form update lokasi (menggunakan method PATCH)
Route::patch('/dashboard/logistics/{logistics}/update-location', [LogisticsController::class, 'updateLocation'])
    ->middleware(['auth']) // Pastikan pengguna sudah login
    ->name('logistics.updateLocationAction'); // Nama rute untuk aksi update dari form

// Route::get('/dashboard/logistics/{logistics}/update-location-form', [LogisticsController::class, 'showUpdateLocationForm'])
//     ->middleware(['auth']) // Atau middleware lain yang sesuai, misal ['auth', 'role:distributor']
//     ->name('logistics.updateLocationForm');

// // Rute untuk menampilkan form update lokasi logistik
// Route::get('logistics/{logistics}/edit-location', [LogisticsController::class, 'showUpdateLocationForm'])
//     ->middleware(['auth', 'verified']) // Pastikan user login dan email terverifikasi (opsional untuk verified)
//     ->name('dashboard.logistics.editLocationForm');


// Rute untuk menampilkan halaman detail logistik dan form update sederhana
Route::get('/logistics/{logistics}/view', [LogisticsController::class, 'showLogisticsPage'])
    ->name('logistics.showPage'); // Memberi nama pada rute

// Rute untuk menangani submit form dari halaman tersebut
Route::post('/logistics/{logistics}/request-update', [LogisticsController::class, 'handleFormRequest'])
    ->name('logistics.handleForm'); // Memberi nama pada rute

// Rute Login
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest') // Hanya bisa diakses oleh guest (pengguna yang belum login)
    ->name('login'); // Nama rute opsional

// Rute Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth') // Hanya bisa diakses oleh pengguna yang sudah login
    ->name('logout'); // Nama rute opsional

// Rute Register
Route::post('/register', function () {
    // Logika untuk menyimpan user baru
    // Misalnya, menggunakan User::create() atau Auth::register()
})->middleware('guest')->name('register.post'); // Hanya bisa diakses oleh guest

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () { // Pastikan user login
    Route::resource('stores', StoreController::class);
    // Anda perlu menambahkan middleware untuk cek role 'seller' di controller atau di sini
});

Route::middleware(['auth'])->group(function () {
    Route::resource('stores.wastes', WasteController::class)->scoped(); // Jika waste terkait store
    Route::resource('wastes.variants', WasteVariantController::class)->scoped(); // Jika variant terkait waste
});

Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    // Tambahkan route lain untuk transaction index, update status, dll.
});

Route::middleware(['auth'])->group(function () {
    Route::resource('stores', StoreController::class);
    // Ini akan membuat route seperti /stores/{store}/wastes yang mengarah ke WasteController@index
    Route::resource('stores.wastes', WasteController::class)->scoped([
        // 'store' => 'username' // jika Anda ingin menggunakan kolom selain id untuk binding Store
    ]);
    // Anda juga perlu Route::resource untuk 'wastes.variants' jika ada
    // Route::resource('wastes.variants', WasteVariantController::class)->scoped();
});

Route::middleware(['auth'])->group(function () {
    // ... rute lainnya ...
    Route::resource('wastes.variants', WasteVariantController::class)->scoped();
    // Ini akan otomatis membuat rute untuk:
    // GET    /wastes/{waste}/variants            (wastes.variants.index) -> WasteVariantController@index
    // GET    /wastes/{waste}/variants/create     (wastes.variants.create) -> WasteVariantController@create
    // POST   /wastes/{waste}/variants            (wastes.variants.store) -> WasteVariantController@store
    // GET    /wastes/{waste}/variants/{variant}  (wastes.variants.show) -> WasteVariantController@show (jika diimplementasikan)
    // GET    /wastes/{waste}/variants/{variant}/edit (wastes.variants.edit) -> WasteVariantController@edit
    // PUT    /wastes/{waste}/variants/{variant}  (wastes.variants.update) -> WasteVariantController@update
    // DELETE /wastes/{waste}/variants/{variant}  (wastes.variants.destroy) -> WasteVariantController@destroy (jika diimplementasikan)
});

Route::get('/waste-map', [WasteController::class, 'index'])->name('waste.index');


Route::middleware('auth:sanctum')->group(function () { // Atau middleware 'auth' jika web
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store'); // Buyer
    Route::patch('/transactions/{transaction}/confirm-by-seller', [TransactionController::class, 'confirmBySeller'])->name('transactions.confirm_by_seller'); // Seller atau Admin
    Route::patch('/transactions/{transaction}/pickup-by-distributor', [TransactionController::class, 'markAsPickedUpByDistributor'])->name('transactions.pickup_by_distributor'); // Distributor
    Route::patch('/transactions/{transaction}/deliver-by-distributor', [TransactionController::class, 'markAsDeliveredByDistributor'])->name('transactions.deliver_by_distributor'); // Distributor
    Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel'); // Buyer/Seller/Admin

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});

Route::get('/test-transactions', function () {
    // Ambil beberapa data untuk diisi di form jika perlu, misal daftar waste variants, users, dll.
    $waste_variants = \App\Models\WasteVariant::take(5)->get();
    $users = \App\Models\User::take(5)->get(); // Terutama yang punya peran distributor
    $transactions_pending = Transaction::where('status', Transaction::STATUS_PENDING)->take(5)->get();
    $transactions_confirmed = Transaction::where('status', Transaction::STATUS_CONFIRMED)->take(5)->get();
    $transactions_picked_up = Transaction::where('status', Transaction::STATUS_PICKED_UP)->take(5)->get();

    return view('test_transactions', compact(
        'waste_variants',
        'users',
        'transactions_pending',
        'transactions_confirmed',
        'transactions_picked_up'
    ));
})->middleware('auth'); // Pastikan hanya user terautentikasi yang bisa akses

require __DIR__ . '/auth.php';
