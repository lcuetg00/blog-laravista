<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Estado de activación (Sí/No) de un recurso, respaldado por el int que persisten las columnas booleanas; usado en filtros y badges del panel.
 */
enum ActivadoEnum: int
{
    case SI = 1;

    case NO = 0;

    /**
     * Devuelve la etiqueta traducida del estado (Sí/No).
     */
    public function trans(): string
    {
        return match ($this) {
            self::SI => trans('fields.si'),
            self::NO => trans('fields.no'),
        };
    }
}
