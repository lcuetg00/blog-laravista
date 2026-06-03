<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contrato común para los enums de ordenación (Interfaz)
 */
interface OrdenacionEnum
{
    /**
     * Devuelve el nombre real de la columna en la base de datos para esta clave de ordenación.
     */
    public function getNombreColumna(): string;
}
