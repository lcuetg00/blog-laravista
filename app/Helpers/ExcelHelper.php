<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper con utilidades de seguridad y formato para exportaciones a Excel/CSV.
 */
class ExcelHelper
{
    /**
     * Protege contra Excel/CSV formula injection prefijando un apóstrofo si la celda empieza por =, +, -, @ tras eliminar espacios iniciales.
     *
     * Ejemplos peligrosos (se neutralizan): "=2+2", "@SUM(A1:A10)", "-1+2", "   =HYPERLINK(...)" (con espacios iniciales).
     * Ejemplos seguros (se exportan tal cual): "'=Admin5", "'@Admin", "'-1+2" (un carácter no peligroso al inicio, como el apóstrofo, ya rompe la fórmula).
     */
    public static function sanearFormula(?string $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return $valor;
        }

        // Eliminamos espacios/tabs/saltos iniciales para evitar que un valor como "   =FORMULA" eluda la comprobación
        $valor = ltrim($valor);

        if ($valor === '') {
            return $valor;
        }

        // Si el primer carácter es uno de los que Excel interpreta como fórmula, lo neutralizamos con un apóstrofo inicial
        if (in_array($valor[0], ['=', '+', '-', '@'], true)) {
            return "'" . $valor;
        }

        return $valor;
    }
}
