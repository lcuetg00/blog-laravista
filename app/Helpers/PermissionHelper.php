<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper centralizado con las constantes de los permisos de la aplicación y su descripción para el seeder.
 */
class PermissionHelper
{
    // Permite el acceso al panel de administración
    public const string ACCESO_PANEL_PERMISSION = 'acceso_panel';

    public const string USUARIOS_LISTADO_PERMISSION = 'usuarios_listado';
    public const string USUARIOS_VER_PERMISSION = 'usuarios_ver';
    public const string USUARIOS_CREAR_PERMISSION = 'usuarios_crear';
    public const string USUARIOS_EDITAR_PERMISSION = 'usuarios_editar';
    public const string USUARIOS_ELIMINAR_PERMISSION = 'usuarios_eliminar';
    public const string USUARIOS_RESTAURAR_PERMISSION = 'usuarios_restaurar';
    public const string USUARIOS_EXPORTAR_PERMISSION = 'usuarios_exportar';

    /**
     * Devuelve el mapa completo de permisos de la aplicación indexado por el nombre del permiso y con su descripción asociada (fuente única para el seeder).
     */
    public static function permisos(): array
    {
        return [
            self::ACCESO_PANEL_PERMISSION => [
                'descripcion' => 'Permite el acceso al panel de administración',
            ],
            /** Usuarios */
            self::USUARIOS_LISTADO_PERMISSION => [
                'descripcion' => 'Permite ver el listado de usuarios en el panel',
            ],
            self::USUARIOS_VER_PERMISSION => [
                'descripcion' => 'Permite ver la ficha de detalle de un usuario en el panel',
            ],
            self::USUARIOS_CREAR_PERMISSION => [
                'descripcion' => 'Permite crear nuevos usuarios desde el panel',
            ],
            self::USUARIOS_EDITAR_PERMISSION => [
                'descripcion' => 'Permite editar los datos de los usuarios desde el panel',
            ],
            self::USUARIOS_ELIMINAR_PERMISSION => [
                'descripcion' => 'Permite eliminar (soft delete) usuarios desde el panel',
            ],
            self::USUARIOS_RESTAURAR_PERMISSION => [
                'descripcion' => 'Permite restaurar usuarios previamente eliminados desde el panel',
            ],
            self::USUARIOS_EXPORTAR_PERMISSION => [
                'descripcion' => 'Permite exportar el listado de usuarios a un archivo Excel desde el panel',
            ],
            /** Fin usuarios */
        ];
    }
}
