<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Helper con patrones regex reutilizables para validar campos de texto en FormRequests (Index, Store, Update, Export).
 *
 * Restringen el conjunto de caracteres aceptado a un set seguro para mantener limpios los datos y como defensa en profundidad:
 * la protección real frente a SQL injection la aportan los bindings de PDO/Eloquent, no estos patrones.
 * Todos los patrones usan el modificador "u" (Unicode) para que \p{L} y \p{N} acepten letras y dígitos de cualquier idioma.
 * El lookahead inicial (?!.*--) bloquea cualquier secuencia de dos guiones seguidos (comentario de línea SQL).
 */
class ValidacionHelper
{
    /**
     * Texto libre corto (nombres, apellidos, direcciones, razones sociales): letras, dígitos, espacios y - . , ' & / ( ) : + º. Prohíbe "--" consecutivos.
     */
    public const REGEX_TEXTO = '/^(?!.*--)[\p{L}\p{N}\s\-.,\'&\/():+º]+$/u';

    /**
     * Búsqueda parcial por email: letras, dígitos y @ . - +. Sin espacios ni "--" consecutivos.
     */
    public const REGEX_EMAIL = '/^(?!.*--)[\p{L}\p{N}\-.@+]+$/u';

    /**
     * Campos numéricos formateados (documentos de identidad, códigos postales): dígitos, espacios y - . + ( ). Prohíbe "--" consecutivos.
     */
    public const REGEX_NUMERICO = '/^(?!.*--)[\p{N}\s\-.+()]+$/u';

    /**
     * Números de teléfono (dígitos, espacios y los símbolos "+ - ( )") Prohíbe "--" consecutivos.
     */
    public const REGEX_TELEFONO = '/^(?!.*--)[\p{N}\s\-+()]+$/u';

    /**
     * Validación para parte de URL (letras ASCII, dígitos y guión, sin espacios ni guión bajo)
     */
    public const REGEX_SLUG = '/^[A-Za-z0-9-]+$/';

    /**
     * MIME types de imagen aceptados al subir ficheros, indexados por extensión (fuente de la regla App\Rules\MimeTypeImagenValido).
     */
    public const MIME_TYPES_IMAGEN = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];

    /**
     * Tamaño máximo en kilobytes permitido para una imagen subida (regla max: de los ficheros de bloques; 4096 KB = 4 MB).
     */
    public const MAX_KB_IMAGEN = 4096;

    /**
     * Convierte recursivamente a null las cadenas vacías o de solo espacios, recortando el resto
     */
    public static function nullificarVacios(array|string|null $valor): array|string|null
    {
        if (is_array($valor)) {
            return array_map([self::class, 'nullificarVacios'], $valor);
        }

        if ($valor === null) {
            return null;
        }

        // Tratamos el blanco (vacío o solo espacios) como ausencia de valor para que 'nullable' lo salte y 'required' lo rechace
        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }
}
