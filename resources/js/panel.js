// Tema: efectos a nivel de módulo (window.changePageMode y listener de preferencia del sistema)
import './shared/theme.js';
import { initTheme } from './shared/theme.js';
import { initSidebar } from './panel/sidebar.js';
import { initPreviewModal } from './panel/preview.js';
import { initPopups } from './panel/popups.js';
import { initToasts } from './panel/toasts.js';
import { initLivewireBridge } from './panel/livewire-bridge.js';
import { initSummernote, actualizarSummernote, destruirSummernote } from './panel/summernote.js';

// Se exponen en window a nivel de módulo (no dentro de DOMContentLoaded): Alpine arranca en su propio listener de
// DOMContentLoaded, registrado antes que el de este archivo (app.js se carga primero), así que si un x-init las
// necesitara dentro de ese mismo evento ya tendrían que existir.
window.initSummernote = initSummernote;
window.actualizarSummernote = actualizarSummernote;
window.destruirSummernote = destruirSummernote;

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initPreviewModal();
    initPopups();
    initToasts();
    initLivewireBridge();
});
