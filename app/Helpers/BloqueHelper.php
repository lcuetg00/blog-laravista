<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Bloque;
use App\Rules\MimeTypeImagenValido;
use Closure;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * Construye dinámicamente las reglas, atributos y mensajes de validación de un bloque a partir de la metadata de su tipo (BloqueTipoEnum::campos()).
 *
 * Fuente única reutilizada por el componente Livewire de edición de bloques: separa los campos escalares/repetidores (que viven en la propiedad
 * 'campos' del componente) de los ficheros de imagen (imagen única, galería nueva y alt de las existentes, con sus propias propiedades).
 */
class BloqueHelper
{
    /**
     * Devuelve las reglas de validación del bloque mapeadas a las propiedades del componente Livewire (campos.*, imagen, galeriaNuevas.*, alts.*).
     */
    public static function reglas(Bloque $bloque): array
    {
        $reglas = [];
        $idiomas = LaravelLocalization::getSupportedLanguagesKeys();
        $campos = $bloque->tipo->campos();

        // Solo exigimos el contenedor 'campos' si el tipo tiene algún campo traducible o repetidor (la media se valida aparte)
        $tieneTraducibles = collect($campos)->contains(fn ($d) => self::esCampoTraducible($d) || self::esRepetidor($d['tipo']));
        if ($tieneTraducibles) {
            $reglas['campos'] = ['required', 'array'];
        }

        foreach ($campos as $clave => $definicion) {
            $tipo = $definicion['tipo'];
            $requerido = ($definicion['requerido'] ?? false) ? 'required' : 'nullable';

            // Imagen única (icono, imagen del HERO): fichero subido validado por su MIME real, indexado por la clave del campo
            if ($tipo === 'imagen') {
                $reglas['imagenes.' . $clave] = self::reglasImagen($requerido);

                continue;
            }

            // Galería: ficheros nuevos validados por MIME real y alt editable de las imágenes existentes
            if ($tipo === 'galeria') {
                $reglas['galeriaNuevas'] = ['nullable', 'array'];
                $reglas['galeriaNuevas.*'] = self::reglasImagen();
                $reglas['alts'] = ['nullable', 'array'];

                foreach ($idiomas as $idioma) {
                    $reglas['alts.*.' . $idioma] = self::reglasTextoCorto('nullable', 255);
                }

                continue;
            }

            // Carrusel etiquetado: cada item es una imagen con su título traducible (filas nuevas) y el título editable de los existentes
            if ($tipo === 'galeria_etiquetada') {
                $reglas['etiquetas'] = ['nullable', 'array'];
                $reglas['nuevosItems'] = ['nullable', 'array'];
                $reglas['nuevosItems.*.imagen'] = self::reglasImagen('required');

                foreach ($idiomas as $idioma) {
                    $reglas['etiquetas.*.' . $idioma] = self::reglasTextoCorto('required', 100);
                    $reglas['nuevosItems.*.etiqueta.' . $idioma] = self::reglasTextoCorto('required', 100);
                }

                // Si el campo es requerido, debe quedar al menos un item entre los existentes y las filas nuevas
                if (($definicion['requerido'] ?? false) && isset($definicion['coleccion'])) {
                    $coleccion = $definicion['coleccion'];
                    $reglas['nuevosItems'][] = static function (string $attribute, mixed $value, Closure $fail) use ($bloque, $coleccion): void {
                        if ($bloque->getMedia($coleccion)->count() + count($value ?? []) < 1) {
                            $fail(trans('fields.bloques.carrusel.requerido'));
                        }
                    };
                }

                continue;
            }

            // Traducibles (escalares, enum y repetidores): una regla por idioma soportado bajo campos.{idioma}.{clave}
            foreach ($idiomas as $idioma) {
                $base = 'campos.' . $idioma . '.' . $clave;

                if (self::esRepetidor($tipo)) {
                    $reglas[$base] = [$requerido, 'array'];
                } elseif ($tipo === 'enum_local') {
                    $reglas[$base] = [$requerido, Rule::in($definicion['valores'])];
                } else {
                    $reglas[$base] = self::reglasEscalar($definicion, $requerido);
                }
            }
        }

        return $reglas;
    }

    /**
     * Devuelve los nombres legibles de todos los campos del bloque (sufijando los traducibles por idioma) para los mensajes de validación.
     */
    public static function atributos(Bloque $bloque): array
    {
        $atributos = [];
        $idiomas = LaravelLocalization::getSupportedLanguagesKeys();

        foreach ($bloque->tipo->campos() as $clave => $definicion) {
            $etiqueta = trans('fields.bloques.campos.' . $clave);

            // Imagen única: el fichero viaja en la propiedad 'imagenes' indexada por la clave del campo
            if ($definicion['tipo'] === 'imagen') {
                $atributos['imagenes.' . $clave] = $etiqueta;

                continue;
            }

            // Galería: los ficheros nuevos viajan en la propiedad 'galeriaNuevas'
            if ($definicion['tipo'] === 'galeria') {
                $atributos['galeriaNuevas'] = $etiqueta;
                $atributos['galeriaNuevas.*'] = $etiqueta;

                continue;
            }

            // Carrusel etiquetado: imagen y título de cada fila nueva y título de los items existentes
            if ($definicion['tipo'] === 'galeria_etiquetada') {
                $atributos['nuevosItems.*.imagen'] = trans('fields.bloques.carrusel.imagen');

                foreach ($idiomas as $idioma) {
                    $atributos['nuevosItems.*.etiqueta.' . $idioma] = trans('fields.bloques.carrusel.etiqueta') . ' (' . $idioma . ')';
                    $atributos['etiquetas.*.' . $idioma] = trans('fields.bloques.carrusel.etiqueta') . ' (' . $idioma . ')';
                }

                continue;
            }

            // Traducibles (escalares, enum y repetidores): una clave por idioma bajo campos.{idioma}.{clave}
            foreach ($idiomas as $idioma) {
                $atributos['campos.' . $idioma . '.' . $clave] = $etiqueta . ' (' . $idioma . ')';
            }
        }

        return $atributos;
    }

    /**
     * Mensaje específico cuando un repetidor no contiene un array válido, remitiendo al aviso de estructura JSON.
     */
    public static function mensajes(Bloque $bloque): array
    {
        $mensajes = [];
        $idiomas = LaravelLocalization::getSupportedLanguagesKeys();

        foreach ($bloque->tipo->campos() as $clave => $definicion) {
            if (self::esRepetidor($definicion['tipo'])) {
                foreach ($idiomas as $idioma) {
                    $mensajes['campos.' . $idioma . '.' . $clave . '.array'] = trans('fields.bloques.json_aviso');
                }
            }
        }

        return $mensajes;
    }

    /**
     * Indica si un campo se gestiona como medios subidos (imagen única o galería) en lugar de dentro del JSON campos.
     */
    public static function esCampoMedia(string $tipo): bool
    {
        return in_array($tipo, ['imagen', 'galeria', 'galeria_etiquetada'], true);
    }

    /**
     * Indica si un tipo lógico de campo es un repetidor (se edita como JSON crudo en este corte).
     */
    public static function esRepetidor(string $tipo): bool
    {
        return in_array($tipo, ['repetidor_json', 'repetidor_traducible', 'matriz_traducible'], true);
    }

    /**
     * Indica si un campo es traducible (se edita con un valor por idioma en pestañas).
     */
    public static function esCampoTraducible(array $definicion): bool
    {
        return $definicion['traducible'] ?? false;
    }

    /**
     * Devuelve las reglas de un fichero de imagen subido (mimes desde la fuente única de extensiones y filtrado real por MimeTypeImagenValido).
     */
    private static function reglasImagen(string $requerido = 'nullable'): array
    {
        return [$requerido, 'file', 'mimes:' . implode(',', array_keys(ValidacionHelper::MIME_TYPES_IMAGEN)), new MimeTypeImagenValido, 'max:' . ValidacionHelper::MAX_KB_IMAGEN];
    }

    /**
     * Devuelve las reglas de un texto corto traducible (regex de texto del proyecto y longitud máxima dada).
     */
    private static function reglasTextoCorto(string $requerido, int $max): array
    {
        return [$requerido, 'string', 'regex:' . ValidacionHelper::REGEX_TEXTO, 'max:' . $max];
    }

    /**
     * Devuelve las reglas de un valor escalar (string/text/url) aplicando regex solo a los string cortos y url a los enlaces.
     */
    private static function reglasEscalar(array $definicion, string $requerido): array
    {
        $reglas = [$requerido, 'string'];

        // Los enlaces se validan como URL http/https; los string cortos con la regex de texto del proyecto.
        // Los campos 'text' (descripciones, subtítulos) quedan libres para admitir puntuación y saltos de línea de cualquier idioma
        if ($definicion['tipo'] === 'url') {
            $reglas[] = 'url:http,https';
        } elseif ($definicion['tipo'] === 'string') {
            $reglas[] = 'regex:' . ValidacionHelper::REGEX_TEXTO;
        }

        if (isset($definicion['max'])) {
            $reglas[] = 'max:' . $definicion['max'];
        }

        return $reglas;
    }
}
