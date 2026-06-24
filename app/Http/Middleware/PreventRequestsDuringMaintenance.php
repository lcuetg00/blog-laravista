<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Extiende el middleware de mantenimiento de Laravel para aceptar también el secreto con el prefijo de idioma ("/es/secreto").
 */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Comprueba primero el secreto con prefijo de idioma antes de delegar en el middleware original de Laravel.
     */
    public function handle($request, Closure $next): Response
    {
        try {
            if ($this->app->maintenanceMode()->active()) {
                $data = $this->app->maintenanceMode()->data();

                if (isset($data['secret']) && $this->pathEsSecretoConIdioma($request->path(), $data['secret'])) {
                    // Usa el msmo original, si coiincide el secreto que se puso en el mantenimiento
                    return $this->bypassResponse($data['secret']);
                }
            }
        } catch (Throwable) {
            // Si algo falla al leer el estado de mantenimiento, dejamos que el middleware original lo gestione
        }

        return parent::handle($request, $next);
    }

    /**
     * Determina si el path solicitado es el secreto precedido por alguno de los idiomas soportados.
     */
    private function pathEsSecretoConIdioma(string $path, string $secreto): bool
    {
        foreach (LaravelLocalization::getSupportedLanguagesKeys() as $idioma) {
            if ($path === "{$idioma}/{$secreto}") {
                return true;
            }
        }

        return false;
    }
}
