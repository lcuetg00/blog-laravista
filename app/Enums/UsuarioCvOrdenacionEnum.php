<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\OrdenacionEnum;

/**
 * Claves válidas para ordenar el listado de CVs de un usuario y su traducción a la columna real de la tabla.
 */
enum UsuarioCvOrdenacionEnum: string implements OrdenacionEnum
{
    case NOMBRE = 'nombre';

    case CREADO_EN = 'created_at';

    case ACTUALIZADO_EN = 'updated_at';

    /**
     * Devuelve el nombre real de la columna en la tabla `usuarios_cvs`.
     */
    public function getNombreColumna(): string
    {
        return match ($this) {
            self::NOMBRE => 'nombre',
            self::CREADO_EN => 'created_at',
            self::ACTUALIZADO_EN => 'updated_at',
        };
    }
}
