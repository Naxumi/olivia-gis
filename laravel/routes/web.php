<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WasteController;
use App\Http\Controllers\WasteVariantController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\TransactionController;
use App\Models\Waste;

Route::get('/', function () {
    return view('welcome');
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


require __DIR__ . '/auth.php';
