/**
 * Inicializa todos los toasts presentes en el DOM y los muestra al cargar la página.
 */
export function initToasts() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
        return;
    }

    const toastEls = document.querySelectorAll('.toast');
    toastEls.forEach((el) => {
        const toast = bootstrap.Toast.getOrCreateInstance(el);
        toast.show();
    });
}

/**
 * Crea un toast en vivo (mismo marcado que los del flash), lo añade al contenedor, lo muestra y lo elimina del DOM al ocultarse.
 */
export function mostrarToast(tipo, mensaje) {
    const contenedor = document.getElementById('toastContainer');
    if (!contenedor || typeof bootstrap === 'undefined' || !bootstrap.Toast) {
        return;
    }

    const esError = tipo === 'error';
    const el = document.createElement('div');
    el.className = `toast toast-panel align-items-center shadow ${esError ? 'toast-danger' : 'toast-success'}`;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');
    el.dataset.bsDelay = '3000';
    el.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center gap-2">
                <span class="toast-icon shadow">
                    <i class="fa-solid ${esError ? 'fa-circle-exclamation' : 'fa-circle-check'}" aria-hidden="true"></i>
                </span>
                <span></span>
            </div>
            <button type="button" class="toast-close shadow me-2 m-auto" data-bs-dismiss="toast" aria-label="${contenedor.dataset.cerrarLabel || ''}">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <div class="progress toast-progress" aria-hidden="true">
            <div class="progress-bar progress-bar-striped"></div>
        </div>`;

    // El mensaje se asigna con textContent para evitar inyección de HTML
    el.querySelector('.toast-body span:last-child').textContent = mensaje;
    contenedor.appendChild(el);

    const toast = bootstrap.Toast.getOrCreateInstance(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    toast.show();
}
