<?php

declare(strict_types=1);

namespace App\Rules;

use App\Helpers\ValidacionHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Regla que valida que el MIME real de una imagen subida figure entre los formatos aceptados en ValidacionHelper::MIME_TYPES_IMAGEN.
 *
 * Lee el MIME del contenido del fichero (no getClientMimeType(), que es manipulable) como defensa frente a extensiones o cabeceras falseadas.
 */
class MimeTypeImagenValido implements ValidationRule
{
    /**
     * Comprueba que el valor sea un fichero subido válido y que su MIME real coincida con uno de los MIME types de imagen aceptados.
     * Esto es por seguridad, para que nunca se pueda subir un archivo que pueda comprometer la seguridad del servidor
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Comprobamos si es válido el archivo
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail('validation.imagen_mime.no_es_fichero')->translate();

            return;
        }

        // La extensión declarada actúa de clave y debe figurar entre los formatos aceptados; su MIME esperado es el valor de esa clave
        $extension = strtolower($value->getClientOriginalExtension());
        $mimeEsperado = ValidacionHelper::MIME_TYPES_IMAGEN[$extension] ?? null;

        // El MIME real debe coincidir con el esperado para esa extensión, atando extensión y contenido para impedir falseos
        // Por ejemplo, una imagen jpg su mime type debe de ser image/jpeg
        if ($mimeEsperado === null || $value->getMimeType() !== $mimeEsperado) {
            $fail('validation.imagen_mime.formato_no_permitido')->translate([
                'formatos' => implode(', ', array_keys(ValidacionHelper::MIME_TYPES_IMAGEN)),
            ]);
        }
    }
}
