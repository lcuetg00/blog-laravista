<?php

namespace Tests\Feature;

use Database\Seeders\PaginaBloqueSeeder;
use Database\Seeders\PaginaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba que el home público responde correctamente.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // El home carga la página con clave 'home' y sus bloques, así que la sembramos
        $this->seed([PaginaSeeder::class, PaginaBloqueSeeder::class]);

        // Sin un request real, mcamara registra las rutas sin prefijo de locale; desactivamos su redirección para no recibir 302
        $this->withoutMiddleware([LaravelLocalizationRedirectFilter::class, LocaleSessionRedirect::class]);

        $this->get(route('home'))
            ->assertOk();
    }
}
