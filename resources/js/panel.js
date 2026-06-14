// Tema: efectos a nivel de módulo (window.changePageMode y listener de preferencia del sistema)
import './shared/theme.js';
import { initTheme } from './shared/theme.js';
import { initSidebar } from './panel/sidebar.js';
import { initPreviewModal } from './panel/preview.js';
import { initPopups } from './panel/popups.js';
import { initToasts } from './panel/toasts.js';
import { initLivewireBridge } from './panel/livewire-bridge.js';

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initPreviewModal();
    initPopups();
    initToasts();
    initLivewireBridge();
});
