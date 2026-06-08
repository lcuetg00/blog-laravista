<?php

namespace Database\Seeders;

use App\Helpers\PermissionHelper;
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
        // Obtenemos la lista de permisos deseados desde el helper centralizado
        $permisos = PermissionHelper::permisos();

        // Recorremos cada permiso y lo creamos o actualizamos según exista en la base de datos
        foreach ($permisos as $nombre => $atributos) {
            Permission::updateOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                $atributos,
            );
        }

        // Eliminamos de la base de datos los permisos que ya no estén declarados en el helper
        Permission::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', array_keys($permisos))
            ->delete();

        // Limpiamos la caché de permisos de Spatie para que los cambios surtan efecto inmediatamente
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
