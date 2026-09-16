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

    /**
     * Devuelve la posición de una clave dentro de un array de ordenación (primero, segundo, ...), o null si no está activa.
     */
    public static function calcularPosicionOrden(array $ordenacion, string $clave): ?int
    {
        if (!array_key_exists($clave, $ordenacion)) {
            return null;
        }

        return array_search($clave, array_keys($ordenacion), true) + 1;
    }

    /**
     * Calcula el icono, la clase del icono, el aria-sort y el aria-label de una cabecera ordenable según su dirección actual.
     *
     * @return array{icono: string, iconoClase: string, ariaSort: string, ariaLabel: string}
     */
    public static function calcularEstadoVisual(?string $dirActual, string $etiqueta): array
    {
        if ($dirActual === 'asc') {
            return [
                'icono' => 'fa-arrow-up',
                'iconoClase' => 'ordenacion-columna-icon-activo text-secondary',
                'ariaSort' => 'ascending',
                'ariaLabel' => trans('fields.ordenacion.ordenar_descendente', ['columna' => $etiqueta]),
            ];
        }

        if ($dirActual === 'desc') {
            return [
                'icono' => 'fa-arrow-down',
                'iconoClase' => 'ordenacion-columna-icon-activo text-secondary',
                'ariaSort' => 'descending',
                'ariaLabel' => trans('fields.ordenacion.quitar_ordenacion', ['columna' => $etiqueta]),
            ];
        }

        // Sin orden: mostramos un icono de flechas arriba/abajo como indicador de que la columna es ordenable
        return [
            'icono' => 'fa-up-down',
            'iconoClase' => 'ordenacion-columna-icon-inactivo',
            'ariaSort' => 'none',
            'ariaLabel' => trans('fields.ordenacion.ordenar_ascendente', ['columna' => $etiqueta]),
        ];
    }
}
