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

    public const string ROLES_LISTADO_PERMISSION = 'roles_listado';

    public const string ROLES_VER_PERMISSION = 'roles_ver';

    public const string ROLES_CREAR_PERMISSION = 'roles_crear';

    public const string ROLES_EDITAR_PERMISSION = 'roles_editar';

    public const string ROLES_ELIMINAR_PERMISSION = 'roles_eliminar';

    public const string ROLES_EXPORTAR_PERMISSION = 'roles_exportar';

    public const string PAGINAS_LISTADO_PERMISSION = 'paginas_listado';

    public const string PAGINAS_VER_PERMISSION = 'paginas_ver';

    public const string PAGINAS_EDITAR_PERMISSION = 'paginas_editar';

    public const string USUARIOS_CVS_LISTADO_PERMISSION = 'usuarios_cvs_listado';

    public const string USUARIOS_CVS_CREAR_PERMISSION = 'usuarios_cvs_crear';

    public const string USUARIOS_CVS_EDITAR_PERMISSION = 'usuarios_cvs_editar';

    public const string USUARIOS_CVS_ELIMINAR_PERMISSION = 'usuarios_cvs_eliminar';

    public const string USUARIOS_CVS_GENERAR_PDF_PERMISSION = 'usuarios_cvs_generar_pdf';

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
            /** Roles */
            self::ROLES_LISTADO_PERMISSION => [
                'descripcion' => 'Permite ver el listado de roles en el panel',
            ],
            self::ROLES_VER_PERMISSION => [
                'descripcion' => 'Permite ver la ficha de detalle de un rol en el panel',
            ],
            self::ROLES_CREAR_PERMISSION => [
                'descripcion' => 'Permite crear nuevos roles desde el panel',
            ],
            self::ROLES_EDITAR_PERMISSION => [
                'descripcion' => 'Permite editar los datos de los roles desde el panel',
            ],
            self::ROLES_ELIMINAR_PERMISSION => [
                'descripcion' => 'Permite eliminar roles desde el panel (excepto los roles protegidos del sistema)',
            ],
            self::ROLES_EXPORTAR_PERMISSION => [
                'descripcion' => 'Permite exportar el listado de roles a un archivo Excel desde el panel',
            ],
            /** Fin roles */
            /** Páginas */
            self::PAGINAS_LISTADO_PERMISSION => [
                'descripcion' => 'Permite ver el listado de páginas en el panel',
            ],
            self::PAGINAS_VER_PERMISSION => [
                'descripcion' => 'Permite ver la ficha de detalle de una página en el panel',
            ],
            self::PAGINAS_EDITAR_PERMISSION => [
                'descripcion' => 'Permite editar los datos de las páginas desde el panel',
            ],
            /** Fin páginas */
            /** CVs de usuario */
            self::USUARIOS_CVS_LISTADO_PERMISSION => [
                'descripcion' => 'Permite ver los CVs y secciones de un usuario en el panel',
            ],
            self::USUARIOS_CVS_CREAR_PERMISSION => [
                'descripcion' => 'Permite crear CVs y secciones de un usuario desde el panel',
            ],
            self::USUARIOS_CVS_EDITAR_PERMISSION => [
                'descripcion' => 'Permite editar CVs y secciones de un usuario desde el panel, incluida su reordenación',
            ],
            self::USUARIOS_CVS_ELIMINAR_PERMISSION => [
                'descripcion' => 'Permite eliminar CVs y secciones de un usuario desde el panel',
            ],
            self::USUARIOS_CVS_GENERAR_PDF_PERMISSION => [
                'descripcion' => 'Permite generar y descargar el PDF de un CV de usuario desde el panel',
            ],
        /** Fin CVs de usuario */
        ];
    }
}
