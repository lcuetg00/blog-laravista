<?php

namespace App\Helpers;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * Helper centralizado de utilidades sobre los idiomas activos de la aplicación
 */
class IdiomaHelper
{
    /**
     * Devuelve los idiomas soportados (locale => nombre nativo) leídos del paquete de localización; con $actualPrimero coloca el idioma actual al inicio.
     */
    public static function getIdiomasActivos(bool $actualPrimero = true): array
    {
        // Leemos los idiomas del paquete para no hardcodearlos: al añadir o quitar un idioma activo todo se actualiza solo
        $idiomas = array_map(fn ($props) => $props['native'], LaravelLocalization::getSupportedLocales());

        // Salimos si no hay que reordenar o si el idioma actual no está entre los soportados
        $idiomaActual = LaravelLocalization::getCurrentLocale();
        if (!$actualPrimero || !isset($idiomas[$idiomaActual])) {
            return $idiomas;
        }

        // Colocamos el idioma actual el primero (el operador + conserva la clave ya presente y descarta la duplicada, manteniendo el orden del resto)
        return [$idiomaActual => $idiomas[$idiomaActual]] + $idiomas;
    }
}
