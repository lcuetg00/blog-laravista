<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    // Rutas públicas
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
        Route::get('/credits', 'credits')->name('credits');
        Route::get('/tecnologias', 'tecnologias')->name('tecnologias');
        Route::get('/proyectos', 'proyectos')->name('proyectos');
        Route::get('/contacto', 'contacto')->name('contacto');
    });

    // Rutas de autenticación (Fortify)
    Route::middleware('guest')->group(function () {
        Route::controller(AuthenticatedSessionController::class)->group(function () {
            Route::get('/administracion-login', 'create')->name('login');
            Route::post('/administracion-login', 'store')->middleware('throttle:login');
        });

        if (Features::enabled(Features::registration())) {
            Route::controller(RegisteredUserController::class)->group(function () {
                Route::get('/register', 'create')->name('register');
                Route::post('/register', 'store');
            });
        }

        if (Features::enabled(Features::resetPasswords())) {
            Route::controller(PasswordResetLinkController::class)->group(function () {
                Route::get('/forgot-password', 'create')->name('password.request');
                Route::post('/forgot-password', 'store')->name('password.email');
            });

            Route::controller(NewPasswordController::class)->group(function () {
                Route::get('/reset-password/{token}', 'create')->name('password.reset');
                Route::post('/reset-password', 'store')->name('password.update');
            });
        }
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    // Rutas del panel
    Route::middleware('auth')->prefix('panel')->name('panel.')->group(function () {
        Route::get('/', [PanelController::class, 'index'])->name('index');

        // CRUDs. Las urls con parámetros se resuelven por el ulid
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::post('usuarios/{usuario}/restore', [UsuarioController::class, 'restore'])
            ->withTrashed()
            ->name('usuarios.restore');
    });
});
