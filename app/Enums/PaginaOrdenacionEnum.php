<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\OrdenacionEnum;

/**
 * Claves de URL válidas para ordenar el listado de páginas y su traducción a la columna real de la tabla.
 */
enum PaginaOrdenacionEnum: string implements OrdenacionEnum
{
    case TITULO = 'titulo';

    case ACTIVO = 'activo';

    /**
     * Devuelve el nombre real de la columna en la tabla `paginas` que se usará en el ORDER BY.
     */
    public function getNombreColumna(): string
    {
        return match ($this) {
            // Ordena por la traducción del locale activo dentro del JSON de titulo (usado por scopeByOrdenacion)
            self::TITULO => 'titulo->' . app()->getLocale(),
            self::ACTIVO => 'activo',
        };
    }
}
