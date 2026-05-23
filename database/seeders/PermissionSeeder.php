<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder que sincroniza la lista de permisos de la aplicación con la base de datos: crea los que falten, actualiza los existentes y elimina los que ya no estén declarados.
 */
class PermissionSeeder extends Seeder
{
    /**
     * Sincroniza los permisos de la aplicación: crea los que falten, actualiza la descripción de los existentes y elimina de la base de datos los que ya no estén en este seeder.
     */
    public function run(): void
    {
        $permisos = [
            'acceso_panel' => [
                'descripcion' => 'Permite el acceso al panel de administración',
            ],
        ];

        foreach ($permisos as $nombre => $atributos) {
            Permission::updateOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                $atributos,
            );
        }

        Permission::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', array_keys($permisos))
            ->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
