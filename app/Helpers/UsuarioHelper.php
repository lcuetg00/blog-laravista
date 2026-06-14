<?php

namespace App\Helpers;

use App\Enums\RoleEnum;
use App\Models\Usuario;

/**
 * Utilizado para las gestiones que se realicen los usuarios
 */
class UsuarioHelper
{
    /**
     * Determina si el usuario activo puede modificar a otro usuario aplicando la jerarquía de roles y la regla de autoedición (solo el superadmin puede editarse a sí mismo).
     */
    public static function puedeModificarUsuario(?Usuario $usuarioAutenticado, ?Usuario $usuario): bool
    {
        if ($usuarioAutenticado === null || $usuario === null) {
            return false;
        }

        // Nadie puede editarse a sí mismo salvo el superadmin
        if ($usuarioAutenticado->is($usuario) && !RoleHelper::tieneRolSuperadmin($usuarioAutenticado)) {
            return false;
        }

        return RoleHelper::puedeGestionarUsuario($usuarioAutenticado, $usuario);
    }

    /**
     * Determina si el usuario activo puede borrar a otro usuario aplicando la jerarquía de roles y la regla de autoeliminación (nadie puede borrarse a sí mismo, ni siquiera el superadmin).
     */
    public static function puedeBorrarUsuario(?Usuario $usuarioAutenticado, ?Usuario $usuario): bool
    {
        if ($usuarioAutenticado === null || $usuario === null) {
            return false;
        }

        // Nadie puede borrarse a sí mismo, incluido el superadmin
        if ($usuarioAutenticado->is($usuario)) {
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
