<?php

namespace App\Models;

use App\Traits\HasPublicUlid;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Modelo Role propio de la aplicación que extiende el de Spatie. Configurado como modelo activo en config/permission.php para poder añadir lógica/relaciones propias sin tocar el paquete.
 */
class Role extends SpatieRole
{
    use HasPublicUlid;
}
