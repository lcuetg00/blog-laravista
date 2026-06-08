<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\OrdenacionEnum;

/**
 * Claves de URL válidas para ordenar el listado de roles y su traducción a la columna real de la tabla.
 */
enum RoleOrdenacionEnum: string implements OrdenacionEnum
{
    case NOMBRE = 'nombre';

    case DESCRIPCION = 'descripcion';

    /**
     * Devuelve el nombre real de la columna en la tabla `roles`.
     */
    public function getNombreColumna(): string
    {
        return match ($this) {
            self::NOMBRE => 'name',
            self::DESCRIPCION => 'descripcion',
        };
    }
}
