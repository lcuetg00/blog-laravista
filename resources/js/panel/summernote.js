// jquery-global.js debe ser el primer import: pone window.jQuery antes de que se evalúen los imports de Summernote
// de abajo (los imports de un módulo se evalúan todos antes que su propio código, así que si esa asignación
// estuviera aquí mismo en vez de en un módulo separado, llegaría tarde).
import jQuery from './jquery-global.js';

import 'summernote/dist/summernote-lite.css';
import 'summernote/dist/summernote-lite.js';
import 'summernote/dist/lang/summernote-es-ES.js';
import 'summernote/dist/lang/summernote-ja-JP.js';

// Códigos de idioma de Summernote por locale de Laravel (inglés es el idioma por defecto del plugin, no requiere import)
const IDIOMAS_SUMMERNOTE = {
    es: 'es-ES',
    ja: 'ja-JP',
};

// Toolbar sin imagen/vídeo/tabla (las imágenes ya tienen su propia galería en el formulario)
const TOOLBAR = [
    ['style', ['style']],
    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['insert', ['link']],
    ['view', ['codeview']],
];

/**
 * Inicializa Summernote sobre el textarea indicado con el contenido y el idioma dados, avisando de cada cambio con onChange.
 */
export function initSummernote(textarea, { valorInicial = '', lang = 'en', onChange } = {}) {
    const $textarea = jQuery(textarea);

    $textarea.summernote({
        lang: IDIOMAS_SUMMERNOTE[lang] ?? 'en-US',
        height: 200,
        toolbar: TOOLBAR,
        callbacks: {
            onChange: (contenido) => onChange?.(contenido),
        },
    });

    $textarea.summernote('code', valorInicial);
}

/**
 * Sustituye el contenido del editor solo si difiere del actual, para no interrumpir al usuario ni disparar onChange en bucle.
 */
export function actualizarSummernote(textarea, contenido) {
    const $textarea = jQuery(textarea);

    if ($textarea.summernote('code') === contenido) {
        return;
    }

    $textarea.summernote('code', contenido);
}

/**
 * Destruye la instancia de Summernote del textarea indicado.
 */
export function destruirSummernote(textarea) {
    jQuery(textarea).summernote('destroy');
}
