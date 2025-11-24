<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/credits', [HomeController::class, 'credits'])->name('credits');
    Route::get('/tecnologias', [HomeController::class, 'tecnologias'])->name('tecnologias');
    Route::get('/proyectos', [HomeController::class, 'proyectos'])->name('proyectos');
    Route::get('/contacto', [HomeController::class, 'contacto'])->name('contacto');
});
