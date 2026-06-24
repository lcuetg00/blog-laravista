<?php

use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as PreventRequestsDuringMaintenanceBase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'localize' => LaravelLocalizationRoutes::class,
            'localizationRedirect' => LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => LocaleSessionRedirect::class,
            'localeCookieRedirect' => LocaleCookieRedirect::class,
            'localeViewPath' => LaravelLocalizationViewPath::class,
            'role' => RoleMiddleware::class,
        ]);

        // Sustituimos el middleware de mantenimiento por el propio, que acepta el secreto con prefijo de idioma
        $middleware->replace(PreventRequestsDuringMaintenanceBase::class, PreventRequestsDuringMaintenance::class);

        // Evitamos cifrar la cookie de bypass de mantenimiento: el middleware de mantenimiento la lee
        // antes de que EncryptCookies (grupo "web") pueda descifrarla
        // Lo utilizamos para que desde el panel, al activar el modo mantenimiento con un secreto, puedas
        // volver al panel
        $middleware->encryptCookies(except: ['laravel_maintenance']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
