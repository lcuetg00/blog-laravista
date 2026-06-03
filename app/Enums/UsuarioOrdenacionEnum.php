<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\OrdenacionEnum;

/**
 * Claves de URL válidas para ordenar el listado de usuarios y su traducción a la columna real de la tabla.
 */
enum UsuarioOrdenacionEnum: string implements OrdenacionEnum
{
    case NOMBRE = 'nombre';

    case PRIMER_APELLIDO = 'primerApellido';

    case SEGUNDO_APELLIDO = 'segundoApellido';

    case EMAIL = 'correoElectronico';

    /**
     * Devuelve el nombre real de la columna en la tabla `usuarios`.
     */
    public function getNombreColumna(): string
    {
        return match ($this) {
            self::NOMBRE => 'nombre',
            self::PRIMER_APELLIDO => 'primer_apellido',
            self::SEGUNDO_APELLIDO => 'segundo_apellido',
            self::EMAIL => 'email',
        };
    }
}
