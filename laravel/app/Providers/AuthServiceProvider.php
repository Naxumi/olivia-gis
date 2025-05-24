<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate; // Jika Anda menggunakan Gate
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Store;       // Tambahkan use statement untuk model Anda
use App\Policies\StorePolicy; // Tambahkan use statement untuk policy Anda

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Contoh bawaan
        Store::class => StorePolicy::class, // <-- BARIS YANG ANDA TAMBAHKAN/MODIFIKASI
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}