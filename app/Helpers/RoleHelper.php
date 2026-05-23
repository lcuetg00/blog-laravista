<?php

namespace App\Helpers;

use App\Enums\RoleEnum;
use App\Models\Usuario;

/**
 * Helper centralizado de comprobaciones sobre roles
 */
class RoleHelper
{
    /**
     * Comprueba si el usuario indicado tiene el rol de superadministrador (id RoleEnum::SUPERADMIN).
     */
    public static function tieneRolSuperadmin(Usuario $usuario): bool
    {
        return $usuario->hasRole(RoleEnum::SUPERADMIN->value);
    }

    /**
     * Comprueba si el usuario indicado tiene el rol de administrador (id RoleEnum::ADMIN).
     */
    public static function tieneRolAdministrador(Usuario $usuario): bool
    {
        return $usuario->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Comprueba si el usuario indicado es administrador a cualquier nivel: superadmin o admin.
     */
    public static function esAdministrador(Usuario $usuario): bool
    {
        return self::tieneRolSuperadmin($usuario) || self::tieneRolAdministrador($usuario);
    }

    /**
     * Determina si $usuarioAutenticado tiene jerarquía suficiente sobre $usuario según los roles definidos en RoleEnum: superadmin puede actuar sobre cualquiera, admin sobre admin/usuario, usuario solo sobre usuario.
     */
    public static function puedeGestionarUsuario(Usuario $usuarioAutenticado, Usuario $usuario): bool
    {
        if ($usuarioAutenticado->hasRole(RoleEnum::SUPERADMIN->value)) {
            // El superadmin puede gestionar cualquier usuario
            return true;
        }

        if ($usuarioAutenticado->hasRole(RoleEnum::ADMIN->value)) {
            // Los admins solo pueden gestionar otros admins y usuarios
            // Nunca deben de gestionar superadmins
            return !self::tieneRolSuperadmin($usuario) && $usuario->hasAnyRole([
                RoleEnum::ADMIN->value,
                RoleEnum::USUARIO->value,
            ]);
        }

        if ($usuarioAutenticado->hasRole(RoleEnum::USUARIO->value)) {
            // Los Usuarios normales solo pueden editar a otros usuarios de momento
            return $usuario->hasRole(RoleEnum::USUARIO->value);
        }

        return false;
    }
}
