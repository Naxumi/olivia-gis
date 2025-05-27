<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'transaction' => 'App\Models\Transaction',
            'review' => 'App\Models\Review',
            // 'referral' => 'App\Models\Referral', // Jika Anda punya model Referral
            // Tambahkan pemetaan lain jika diperlukan
        ]);
    }
}
