import { actualizarPreview, initPreviewPanel } from './preview.js';
import { initPopups } from './popups.js';
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

    // Refresco de la vista previa de la página (título + iframe) cuando se guarda cualquier cambio o se borra una imagen
    window.Livewire.on('recargar-preview', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        actualizarPreview('paginaPreview', datos);
    });

    // Refresco de la vista previa del PDF del CV (modal de secciones): el panel se inserta de forma perezosa (solo
    // existe mientras hay un CV en edición), así que hay que (re)engancharle los listeners de drag/resize/minimizar
    // y de popup antes de recargarlo
    window.Livewire.on('recargar-preview-cv', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        initPreviewPanel('cvPreviewSecciones');
        initPopups();
        actualizarPreview('cvPreviewSecciones', datos);
    });

    // Refresco de la vista previa del PDF del CV (modal del formulario de nombre/colores/tamaños)
    window.Livewire.on('recargar-preview-cv-form', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        initPreviewPanel('cvPreviewForm');
        initPopups();
        actualizarPreview('cvPreviewForm', datos);
    });

    // Vista previa del PDF del CV: cada panel solo es visible mientras su modal está realmente abierto (el servidor
    // no resetea el CV en edición al cerrar el modal, así que la visibilidad no puede depender de su estado)
    const modalSecciones = document.getElementById('modalSecciones');
    if (modalSecciones) {
        modalSecciones.addEventListener('shown.bs.modal', () => {
            document.getElementById('cvPreviewSecciones')?.classList.remove('d-none');
        });
        modalSecciones.addEventListener('hidden.bs.modal', () => {
            document.getElementById('cvPreviewSecciones')?.classList.add('d-none');
        });
    }

    const modalCv = document.getElementById('modalCv');
    if (modalCv) {
        modalCv.addEventListener('shown.bs.modal', () => {
            document.getElementById('cvPreviewForm')?.classList.remove('d-none');
        });
        modalCv.addEventListener('hidden.bs.modal', () => {
            document.getElementById('cvPreviewForm')?.classList.add('d-none');
        });
    }

    // Avisos en vivo emitidos por los componentes Livewire (guardar bloque/página, borrar imagen, ...)
    window.Livewire.on('toast', (evento) => {
        const datos = Array.isArray(evento) ? evento[0] : evento;
        mostrarToast(datos.tipo, datos.mensaje);
    });

    // Cierra el modal de confirmación de borrado de un CV tras eliminarlo correctamente
    window.Livewire.on('cv-eliminado', () => {
        const modalEl = document.getElementById('modalEliminarCv');
        if (modalEl) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
    });
}
