<?php

declare(strict_types=1);

return [
    // Acciones genéricas
    'back' => 'Volver',
    'back_top' => 'Volver arriba',

    // Auth
    'sign_in' => 'Entrar',
    'sign_out' => 'Cerrar sesión',
    'forgot_password' => '¿Olvidaste tu contraseña?',
    'show_password' => 'Mostrar contraseña',
    'hide_password' => 'Ocultar contraseña',

    // CRUD - botones
    'create' => 'Crear',
    'edit' => 'Editar',
    'show' => 'Ver',
    'delete' => 'Eliminar',
    'save' => 'Guardar',
    'accept' => 'Aceptar',
    'cancel' => 'Cancelar',
    'close' => 'Cerrar',
    'export' => 'Exportar',
    'filter' => 'Filtros',
    'filter_submit' => 'Buscar',
    'clear_filters' => 'Borrar filtros',
    'clear_ordenacion' => 'Borrar ordenación',

    // Modal de confirmación de borrado genérico
    'delete_confirm_title' => '¿Desea borrar este registro?',
    'delete_confirm_description' => 'Se eliminará el registro',

    // Mensajes flash genéricos con género gramatical ({1}=Masculino, {2}=Femenino)
    'created' => '{1} :modelo creado correctamente.|{2} :modelo creada correctamente.',
    'updated' => '{1} :modelo actualizado correctamente.|{2} :modelo actualizada correctamente.',
    'deleted' => '{1} :modelo eliminado correctamente.|{2} :modelo eliminada correctamente.',
    'restored' => '{1} :modelo restaurado correctamente.|{2} :modelo restaurada correctamente.',

    // Flash genérico de error cuando una operación falla
    'generic_error' => 'Ha ocurrido un error al procesar la operación. Por favor, inténtalo de nuevo.',

    // Flash de la sección de configuración
    'settings_saved' => 'Los ajustes se han guardado correctamente.',
    'cache_cleared' => 'La caché se ha limpiado correctamente.',
    'views_cleared' => 'Las vistas compiladas se han eliminado correctamente.',
    'maintenance_on' => 'La aplicación está ahora en modo mantenimiento. Usted conserva el acceso al panel.',
    'maintenance_off' => 'La aplicación vuelve a estar operativa.',
];
