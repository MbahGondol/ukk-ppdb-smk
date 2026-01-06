<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\CalonSiswa;
use App\Observers\CalonSiswaObserver;

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
        // Logika: Jika URL aplikasi mengandung 'ngrok' atau 'https', paksa semua aset jadi HTTPS
        if (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Nyalakan CCTV untuk CalonSiswa
        CalonSiswa::observe(CalonSiswaObserver::class);
    }
}
