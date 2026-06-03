<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Direcciones admitidas en la ordenación por URL de los listados del panel
 */
enum OrdenacionColumnaEnum: string
{
    case ASCENDENTE = 'asc';

    case DESCENDIENTE = 'desc';
}
