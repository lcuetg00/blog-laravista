<?php

namespace App\Helpers;

use App\Models\Usuario;
use App\Models\UsuarioCv;
use Dompdf\Dompdf;

/**
 * Utilizado para la generación del PDF del CV de un usuario
 */
class CvHelper
{
    /**
     * Alto fijo (px) de la cabecera completa del PDF del CV (avatar y datos personales), que solo se ve en la 1ª
     * página; igual en las dos pasadas de UsuarioController::generarPdfCv() para que la paginación no cambie entre ambas.
     */
    public const ALTO_CABECERA_PDF_CV = 130;

    /**
     * Alto (px) de la banda de continuación (solo color, sin avatar ni datos) que se ve en el <thead> a partir de
     * la 2ª página, en vez de la cabecera completa (que solo aparece una vez, en la 1ª).
     */
    public const ALTO_BANDA_CONTINUACION_PDF_CV = 45;

    /**
     * Margen inferior (px), en blanco, entre la banda de continuación y el contenido que viene justo debajo.
     */
    public const MARGEN_INFERIOR_BANDA_CONTINUACION_PDF_CV = 15;

    /**
     * Determina si el PDF del CV necesita la fuente Noto Sans JP (comprobando el contenido del CV y el propio
     * texto del pie de página en busca de caracteres japoneses), que es la única con esos glifos.
     */
    public static function usarFuenteJaponesaEnPdfCv(Usuario $usuario, UsuarioCv $usuarioCv): bool
    {
        $notoDisponible = is_file(resource_path('fonts/noto-sans-jp/NotoSansJP-Regular.ttf'))
            && is_file(resource_path('fonts/noto-sans-jp/NotoSansJP-Bold.ttf'));

        if (!$notoDisponible) {
            return false;
        }

        // Revisamos si el texto escrito tiene caracteres japoneses con el regex
        // Identifica Hiragana, Katakana (Con Katakana de medio ancho) y Kanjis
        $regexCaracteresJapones = '/[\x{3040}-\x{30FF}\x{4E00}-\x{9FFF}\x{FF66}-\x{FF9F}]/u';
        $textoParaDetectarJapones = $usuario->nombre_completo
            . ' ' . $usuarioCv->secciones->pluck('titulo')->implode(' ')
            . ' ' . $usuarioCv->secciones->pluck('descripcion')->implode(' ')
            . ' ' . trans('fields.usuarios_cvs.pdf.pagina_de');

        return (bool) preg_match($regexCaracteresJapones, $textoParaDetectarJapones);
    }

    /**
     * Registra, vía setCallbacks("end_document"), el pie de página y la banda de continuación de cada página del CV (a diferencia de un <script type="text/php">
     * embebido en la vista, se ejecuta cuando el documento ya está completo, con las coordenadas de la última página ya correctas).
     */
    public static function dibujarPiePaginaCv(Dompdf $dompdf, UsuarioCv $usuarioCv, bool $usarNoto): void
    {
        $altoBanda = self::ALTO_BANDA_CONTINUACION_PDF_CV;

        $hexColorPrimario = ltrim($usuarioCv->color_primario, '#');
        $colorPrimarioRgb = [
            hexdec(substr($hexColorPrimario, 0, 2)) / 255,
            hexdec(substr($hexColorPrimario, 2, 2)) / 255,
            hexdec(substr($hexColorPrimario, 4, 2)) / 255,
        ];

        $plantillaPiePagina = trans('fields.usuarios_cvs.pdf.pagina_de');
        $fuentePiePagina = $usarNoto ? 'Noto Sans JP' : 'DejaVu Sans';

        $dompdf->setCallbacks([
            [
                'event' => 'end_document',
                'f' => function (int $numeroPagina, int $totalPaginas, $canvas, $fontMetrics) use (
                    $altoBanda,
                    $colorPrimarioRgb,
                    $plantillaPiePagina,
                    $fuentePiePagina
                ): void {
                    if ($numeroPagina > 1) {
                        $canvas->filled_rectangle(0, 0, $canvas->get_width(), $altoBanda, $colorPrimarioRgb);
                    }

                    $texto = str_replace([':actual', ':total'], [$numeroPagina, $totalPaginas], $plantillaPiePagina);
                    $fuente = $fontMetrics->getFont($fuentePiePagina);
                    $fontSize = 11;
                    $altoPiePagina = 34;

                    $anchoTexto = $fontMetrics->getTextWidth($texto, $fuente, $fontSize);
                    $altoTexto = $fontMetrics->getFontHeight($fuente, $fontSize);

                    $x = ($canvas->get_width() - $anchoTexto) / 2;
                    $y = $canvas->get_height() - $altoPiePagina + ($altoPiePagina - $altoTexto) / 2;

                    $canvas->text($x, $y, $texto, $fuente, $fontSize, [1, 1, 1]);
                },
            ],
        ]);
    }
}
