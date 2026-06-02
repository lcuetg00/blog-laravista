<?php

namespace App\Enums;

/**
 * Métodos HTTP admitidos en formularios del panel (los que Laravel sabe falsear vía @method).
 */
enum HttpMethodEnum: string
{
    case POST = 'POST';

    case PUT = 'PUT';

    case PATCH = 'PATCH';

    case DELETE = 'DELETE';
}
