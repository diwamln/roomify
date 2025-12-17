<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        // PERBAIKAN: Ganti 'https' menjadi 'http'
        // Kita paksa HTTP agar cocok dengan URL akses Anda (http://localhost:30080)
        if (config('app.env') === 'production') {
            URL::forceScheme('http');
        }

        // Optional: For Breeze asset optimization
        Vite::prefetch(concurrency: 3);
    }
}
