<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Bloque;

/**
 * Catálogo de tipos de bloque que componen una página pública. El valor int se persiste en bloques.tipo (tinyint unsigned).
 *
 * Cada caso define en campos() la metadata de sus campos (fuente única para validación, formulario del panel y futuro render público).
 */
enum BloqueTipoEnum: int
{
    case TITULO = 1;

    case TITULO_SUBTITULO = 2;

    case BLOQUE_TEXTO = 3;

    case HERO = 4;

    case BOTON = 5;

    case SWIPER = 6;

    case CARRUSEL_GIRATORIO = 7;

    case TABLA = 8;

    case TITULO_SUBTITULO_IMAGEN = 9;

    /**
     * Devuelve la metadata de cada campo del tipo (tipo lógico, si es traducible, si es requerido, longitud máxima y, en repetidores, el esquema de cada elemento).
     */
    public function campos(): array
    {
        return match ($this) {
            self::TITULO => [
                'titulo' => ['tipo' => 'string', 'traducible' => true, 'requerido' => true, 'max' => 255],
            ],
            self::TITULO_SUBTITULO, self::BLOQUE_TEXTO => [
                'titulo' => ['tipo' => 'string', 'traducible' => true, 'requerido' => true, 'max' => 255],
                'subtitulo' => ['tipo' => 'text', 'traducible' => true, 'requerido' => false],
            ],
            self::HERO => [
                'titulo' => ['tipo' => 'string', 'traducible' => true, 'requerido' => true, 'max' => 255],
                'descripcion' => ['tipo' => 'text', 'traducible' => true, 'requerido' => false],
                'icono' => ['tipo' => 'imagen', 'coleccion' => Bloque::MEDIA_COLLECTION_ICONO, 'traducible' => false, 'requerido' => false],
                'imagen' => ['tipo' => 'imagen', 'coleccion' => Bloque::MEDIA_COLLECTION_IMAGEN, 'traducible' => false, 'requerido' => false],
                'texto_boton' => ['tipo' => 'string', 'traducible' => true, 'requerido' => false, 'max' => 100],
                'url_boton' => ['tipo' => 'url', 'traducible' => true, 'requerido' => false, 'max' => 500],
            ],
            self::BOTON => [
                'texto' => ['tipo' => 'string', 'traducible' => true, 'requerido' => true, 'max' => 100],
                'url' => ['tipo' => 'url', 'traducible' => true, 'requerido' => true, 'max' => 500],
            ],
            self::SWIPER => [
                'slides' => ['tipo' => 'galeria', 'coleccion' => Bloque::MEDIA_COLLECTION_GALLERY, 'traducible' => false, 'requerido' => true],
            ],
            self::CARRUSEL_GIRATORIO => [
                'direccion' => ['tipo' => 'enum_local', 'traducible' => true, 'requerido' => true, 'valores' => ['normal', 'inverso']],
                'items' => ['tipo' => 'galeria_etiquetada', 'coleccion' => Bloque::MEDIA_COLLECTION_CARRUSEL, 'traducible' => false, 'requerido' => true],
            ],
            self::TABLA => [
                'columnas' => ['tipo' => 'repetidor_traducible', 'traducible' => true, 'requerido' => true],
                'filas' => ['tipo' => 'matriz_traducible', 'traducible' => true, 'requerido' => true],
            ],
            self::TITULO_SUBTITULO_IMAGEN => [
                'titulo' => ['tipo' => 'string', 'traducible' => true, 'requerido' => true, 'max' => 255],
                'subtitulo' => ['tipo' => 'text', 'traducible' => true, 'requerido' => false],
                'icono' => ['tipo' => 'imagen', 'coleccion' => Bloque::MEDIA_COLLECTION_ICONO, 'traducible' => false, 'requerido' => false],
                'imagen' => ['tipo' => 'imagen', 'coleccion' => Bloque::MEDIA_COLLECTION_IMAGEN, 'traducible' => false, 'requerido' => false],
            ],
        };
    }

    /**
     * Devuelve la etiqueta traducida del tipo para mostrarla en la cabecera del acordeón del panel.
     */
    public function etiqueta(): string
    {
        return trans('fields.bloques.tipos.' . $this->nombreBloque());
    }

    /**
     * Devuelve el nombre del partial blade que renderizará el bloque en el público (preparado para el render dinámico futuro, no usado todavía).
     */
    public function vista(): string
    {
        return 'public.bloques.' . $this->nombreBloque();
    }

    /**
     * Devuelve el nombre del caso en snake_case (usado como sufijo en claves de traducción y nombres de vista).
     */
    public function nombreBloque(): string
    {
        return strtolower($this->name);
    }
}
