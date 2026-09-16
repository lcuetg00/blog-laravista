<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Catálogo de tamaños de fuente seleccionables para el PDF del CV, aplicable de forma independiente a la cabecera y al resto del contenido.
 */
enum FontSizeEnum: int
{
    case SMALL = 1;

    case MEDIUM = 2;

    case LARGE = 3;

    /**
     * Devuelve la etiqueta traducida del tamaño para el selector del formulario del CV.
     */
    public function etiqueta(): string
    {
        return trans('fields.usuarios_cvs.font_sizes.' . strtolower($this->name));
    }

    /**
     * Tamaño en píxeles del nombre del usuario en la cabecera del PDF.
     */
    public function sizeNombreCabecera(): int
    {
        return match ($this) {
            self::SMALL => 15,
            self::MEDIUM => 18,
            self::LARGE => 22,
        };
    }

    /**
     * Tamaño en píxeles de cada línea de datos personales (email, fecha de nacimiento...) de la cabecera del PDF.
     */
    public function sizeSubtituloCabecera(): int
    {
        return match ($this) {
            self::SMALL => 9,
            self::MEDIUM => 11,
            self::LARGE => 13,
        };
    }

    /**
     * Tamaño en píxeles del título de cada sección del cuerpo del PDF.
     */
    public function sizeTituloSeccion(): int
    {
        return match ($this) {
            self::SMALL => 15,
            self::MEDIUM => 18,
            self::LARGE => 21,
        };
    }

    /**
     * Tamaño en píxeles del texto de la descripción de cada sección del cuerpo del PDF.
     */
    public function sizeDescripcionSeccion(): int
    {
        return match ($this) {
            self::SMALL => 9,
            self::MEDIUM => 11,
            self::LARGE => 13,
        };
    }
}
