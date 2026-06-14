<?php

namespace App\Enums;

/**
 * Enum de roles que corresponden con los ids en base de datos de los roles
 * Estos roles siguen una jerarquía de roles de forma
 * Superadmin > Admin > Usuario
 */
enum RoleEnum: int
{
    // Superadministrador, lo puede hacer todo
    case SUPERADMIN = 1;

    // Administrador, rol por debajo de superadministrador. Usa los permisos
    case ADMIN = 2;

    // Ahora mismo este rol no se usará, pero a futuro si quiero que un usuario concreto tenga solo x permisos, será este
    case USUARIO = 3;
}
