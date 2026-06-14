<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pagina;
use Illuminate\Support\Facades\Cache;

/**
 * Centraliza la configuración importante de la página (páginas activas, colores, ...) sirviéndola desde la caché de Laravel.
 */
class ConfiguracionService
{
    /** Clave de caché bajo la que se guardan las claves de páginas activas (reutilizada al invalidar tras editar una página). */
    public const string CACHE_PAGINAS_ACTIVAS = 'configuracion:paginas_activas';

    /** Segundos (un día) que se mantienen cacheadas las claves de páginas activas antes de volver a consultar la BD. */
    private const int TTL_PAGINAS_ACTIVAS = 86400;

    /**
     * Devuelve las claves de las páginas activas desde la caché, consultando la BD solo cuando expira el TTL.
     */
    public static function clavesActivas(): array
    {
        return Cache::remember(
            self::CACHE_PAGINAS_ACTIVAS,
            self::TTL_PAGINAS_ACTIVAS,
            // Si no están, las busca en la base de datos para guardarlas
            fn (): array => Pagina::query()
                ->where('activo', true)
                ->pluck('clave')
                ->all()
        );
    }

    /**
     * Indica si la página identificada por su clave existe y está activa.
     */
    public static function estaActiva(string $clave): bool
    {
        return in_array($clave, self::clavesActivas(), true);
    }
}
