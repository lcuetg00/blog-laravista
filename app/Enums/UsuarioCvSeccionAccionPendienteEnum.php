<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Acción que el usuario intentó ejecutar mientras había cambios sin guardar en el formulario de una sección, pendiente de confirmar su descarte.
 */
enum UsuarioCvSeccionAccionPendienteEnum: string
{
    case CERRAR_MODAL = 'cerrar_modal';

    case CREAR_SECCION = 'crear_seccion';

    case SELECCIONAR_SECCION = 'seleccionar_seccion';
}
