<?php

namespace App\Helpers;

use App\Enums\RoleEnum;
use App\Models\Usuario;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Utilizado para las gestiones que se realicen los usuarios
 */
class UsuarioHelper
{
    /**
     * Determina si el usuario activo puede modificar a otro usuario según la jerarquía de roles definida en RoleEnum.
     */
    public static function puedeModificarUsuario(?Usuario $usuarioAutenticado, ?Usuario $usuario): bool
    {
        if ($usuarioAutenticado === null || $usuario === null) {
            return false;
        }

        return RoleHelper::puedeGestionarUsuario($usuarioAutenticado, $usuario);
    }

    /**
     * Determina si el usuario activo puede borrar a otro usuario según la jerarquía de roles definida en RoleEnum.
     */
    public static function puedeBorrarUsuario(?Usuario $usuarioAutenticado, ?Usuario $usuario): bool
    {
        if ($usuarioAutenticado === null || $usuario === null) {
            return false;
        }

        return RoleHelper::puedeGestionarUsuario($usuarioAutenticado, $usuario);
    }

    /**
     * Determina si el usuario activo puede restaurar otro usuario según la jerarquía de roles definida en RoleEnum.
     */
    public static function puedeRestaurarUsuario(?Usuario $usuarioAutenticado, ?Usuario $usuario): bool
    {
        if ($usuarioAutenticado === null || $usuario === null) {
            return false;
        }

        return RoleHelper::puedeGestionarUsuario($usuarioAutenticado, $usuario);
    }
}
