<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Modelo Permission propio de la aplicación que extiende el de Spatie. Configurado como modelo activo en config/permission.php para poder añadir lógica/relaciones propias sin tocar el paquete.
 */
class Permission extends SpatiePermission
{
    //
}
