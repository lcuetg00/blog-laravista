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
     * Campos numéricos formateados (teléfonos, documentos de identidad, códigos postales): dígitos, espacios y - . + ( ). Prohíbe "--" consecutivos.
     */
    public const REGEX_NUMERICO = '/^(?!.*--)[\p{N}\s\-.+()]+$/u';
}
