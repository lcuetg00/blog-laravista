import jQuery from 'jquery';

// Expone jQuery como variable global antes de cargar Summernote (que la espera así, al ser un plugin de jQuery).
// Va en su propio módulo, importado antes que Summernote: los imports de un módulo se evalúan siempre antes que su
// propio código, así que si esta asignación estuviera en el mismo fichero que los imports de Summernote llegaría tarde.
window.jQuery = jQuery;
window.$ = jQuery;

export default jQuery;
