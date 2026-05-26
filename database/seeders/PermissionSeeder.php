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
        // Definimos la lista de permisos deseados con sus atributos
        $permisos = [
            'acceso_panel' => [
                'descripcion' => 'Permite el acceso al panel de administración',
            ],
            /** Usuarios */
            'usuarios_listado' => [
                'descripcion' => 'Permite ver el listado de usuarios en el panel',
            ],
            'usuarios_crear' => [
                'descripcion' => 'Permite crear nuevos usuarios desde el panel',
            ],
            'usuarios_editar' => [
                'descripcion' => 'Permite editar los datos de los usuarios desde el panel',
            ],
            'usuarios_eliminar' => [
                'descripcion' => 'Permite eliminar (soft delete) usuarios desde el panel',
            ],
            'usuarios_restaurar' => [
                'descripcion' => 'Permite restaurar usuarios previamente eliminados desde el panel',
            ],
        /** Fin usuarios */
        ];

        // Recorremos cada permiso y lo creamos o actualizamos según exista en la base de datos
        foreach ($permisos as $nombre => $atributos) {
            Permission::updateOrCreate(
                ['name' => $nombre, 'guard_name' => 'web'],
                $atributos,
            );
        }

        // Eliminamos de la base de datos los permisos que ya no estén declarados en el array
        Permission::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', array_keys($permisos))
            ->delete();

        // Limpiamos la caché de permisos de Spatie para que los cambios surtan efecto inmediatamente
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
