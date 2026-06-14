<?php

namespace App\Helpers;

/**
 * Helper para convertir entre la cadena de URL compacta y el array asociativo de ordenación.
 * Formato de URL: "campo1:asc?campo2:desc"
 */
class OrdenacionHelper
{
    /**
     * Parsea la cadena de URL en un array asociativo preservando el orden
     *
     * Ejemplo: (clave OrdenacionEnum → OrdenacionColumnaEnum)
     */
    public static function parseCadenaOrdenacion(mixed $cadena): array
    {
        // Si no llega cadena válida devolvemos array vacío directamente
        if (!is_string($cadena) || $cadena === '') {
            return [];
        }

        $resultado = [];

        // Cada par "campo:direccion" va separado por ?
        foreach (explode('?', $cadena) as $pareja) {
            $partes = explode(':', $pareja, 2);

            // Si no hay exactamente dos partes (campo + dirección) descartamos el trozo
            if (count($partes) !== 2) {
                continue;
            }

            [$clave, $direccion] = $partes;

            // Saltamos pares con clave o dirección vacías
            if ($clave === '' || $direccion === '') {
                continue;
            }

            $resultado[$clave] = $direccion;
        }

        return $resultado;
    }

    /**
     * Serializa el array asociativo a la cadena "campo1:asc?campo2:desc". Devuelve null si el array está vacío.
     */
    public static function serializar(array $ordenacion): ?string
    {
        if ($ordenacion === []) {
            return null;
        }

        $partes = [];

        // Iteramos preservando el orden de las claves del array
        foreach ($ordenacion as $clave => $direccion) {
            $partes[] = $clave . ':' . $direccion;
        }

        return implode('?', $partes);
    }
}
