import { actualizarPreview } from './preview.js';
import { mostrarToast } from './toasts.js';

/**
 * Registra los listeners de los eventos de los componentes Livewire de edición (vista previa y toasts en vivo).
 * app.js (en el <head>) ya hizo Livewire.start() antes de ejecutarse este código (footer), así que window.Livewire ya existe;
 * no podemos usar 'livewire:init' porque ya se disparó, por eso registramos los listeners directamente.
 */
export function initLivewireBridge() {
    if (!window.Livewire) {
        return;
    }

    // Refresco de la vista previa (título + iframe) cuando se guarda cualquier cambio o se borra una imagen
    window.Livewire.on('recargar-preview', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        actualizarPreview(datos);
    });

    // Avisos en vivo emitidos por los componentes Livewire (guardar bloque/página, borrar imagen, ...)
    window.Livewire.on('toast', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        mostrarToast(datos.tipo, datos.mensaje);
    });
}
