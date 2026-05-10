<?php

namespace App\Providers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Vincula el LoginRequest de Fortify al propio para personalizar la validación.
     */
    public function register(): void
    {
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            LoginRequest::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar HTTPS cuando APP_URL usa https
        // if (config('app.url') && str_starts_with(config('app.url'), 'https://')) {
        //     URL::forceScheme('https');
        // }
    }
}
