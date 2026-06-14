/**
 * Inicializa los popups CSS-only (.popup[data-popup]): si centrados se saldrían por un borde lateral,
 * los ancla a ese lado para que el texto quepa entero.
 */
export function initPopups() {
    const popups = document.querySelectorAll('.popup[data-popup]');
    if (!popups.length) {
        return;
    }

    const MARGIN = 8;

    /**
     * Recalcula el anclaje del popup (centrado, izquierda o derecha) según su posición actual en el viewport.
     */
    function ajustar(el) {
        // Partimos siempre del centrado por defecto y recalculamos según la posición actual (el popup-sidebar/preview puede moverse)
        el.classList.remove('popup-start', 'popup-end');

        const after = window.getComputedStyle(el, '::after');
        const ancho = parseFloat(after.width) + parseFloat(after.paddingLeft) + parseFloat(after.paddingRight);
        if (!ancho || Number.isNaN(ancho)) {
            return;
        }

        const rect = el.getBoundingClientRect();
        const centro = rect.left + rect.width / 2;

        // Si la mitad del popup rebasa un borde, lo anclamos a ese lado del elemento
        if (centro - ancho / 2 < MARGIN) {
            el.classList.add('popup-start');
        } else if (centro + ancho / 2 > window.innerWidth - MARGIN) {
            el.classList.add('popup-end');
        }
    }

    popups.forEach((el) => {
        el.addEventListener('mouseenter', () => ajustar(el));
        el.addEventListener('focusin', () => ajustar(el));
    });
}
