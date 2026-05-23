<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeder que crea los roles definidos en RoleEnum forzando su id al valor del enum, y, solo la primera vez, asigna todos los permisos al rol ADMIN.
 */
class RoleSeeder extends Seeder
{
    /**
     * Crea los roles definidos en RoleEnum forzando su id al valor del enum, en orden.
     */
    public function run(): void
    {
        $roles = [
            RoleEnum::SUPERADMIN->name => [
                'descripcion' => 'Superadministrador con acceso total a la aplicación',
            ],
            RoleEnum::ADMIN->name => [
                'descripcion' => 'Administrador con acceso al panel mediante permisos asignados',
            ],
            RoleEnum::USUARIO->name => [
                'descripcion' => 'Usuario estándar con permisos individuales asignados',
            ],
        ];

        foreach (RoleEnum::cases() as $role) {
            Role::updateOrCreate(
                ['id' => $role->value],
                array_merge(
                    [
                        'name' => strtolower($role->name),
                        'guard_name' => 'web',
                    ],
                    $roles[$role->name],
                ),
            );
        }

        /**
         * Solo la primera vez que se ejecuta el seeder (cuando el rol ADMIN aún
         * no tiene permisos asignados) se le asignan todos los permisos. En
         * ejecuciones posteriores se respeta la configuración manual del rol.
         */
        $admin = Role::find(RoleEnum::ADMIN->value);

        if ($admin && $admin->permissions()->count() === 0) {
            $admin->syncPermissions(Permission::where('guard_name', 'web')->get());
        }
    }
}
