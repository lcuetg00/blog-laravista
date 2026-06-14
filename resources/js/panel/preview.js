/**
 * Refresca las partes dinámicas de la vista previa tras guardar (título del panel si llega en el evento y el iframe).
 */
export function actualizarPreview(datos) {
    const panel = document.getElementById('paginaPreview');
    if (!panel) {
        return;
    }

    // Actualizamos el título del panel cuando la página ha cambiado de título (los cambios de bloque no lo envían)
    if (datos && datos.titulo != null) {
        const tituloEl = panel.querySelector('[data-preview-title]');
        if (tituloEl) {
            tituloEl.textContent = datos.titulo;
        }
    }

    // Recargamos el iframe solo si ya se había cargado (panel abierto al menos una vez), con un parámetro variable para saltarse la caché
    const iframe = panel.querySelector('iframe');
    if (iframe && iframe.src && iframe.dataset.src) {
        const base = iframe.dataset.src;
        iframe.src = base + (base.includes('?') ? '&' : '?') + 'v=' + Date.now();
    }
}

/**
 * Inicializa el panel flotante de vista previa: anclado abajo a la derecha, arrastrable y redimensionable
 * con Pointer Events (ratón y táctil unificados) y minimizable estilo ventana.
 */
export function initPreviewModal() {
    const panel = document.getElementById('paginaPreview');
    if (!panel) {
        return;
    }

    const iframe = panel.querySelector('iframe');
    const dragHandle = panel.querySelector('[data-preview-drag]');
    const resizer = panel.querySelector('[data-preview-resize]');
    const minimizeBtn = panel.querySelector('[data-preview-minimize]');
    const minimizeIcon = panel.querySelector('[data-preview-minimize-icon]');
    const sizeButtons = panel.querySelectorAll('[data-preview-size]');
    const flipBtn = panel.querySelector('[data-preview-flip]');

    const MIN_W = 260;
    const MIN_H = 200;
    const MARGIN = 8;

    // Presets de ancho/alto aproximados por dispositivo (escritorio compacto, no a pantalla completa)
    const SIZE_PRESETS = {
        movil: { w: 375, h: 667 },
        tablet: { w: 768, h: 1024 },
        escritorio: { w: 1024, h: 640 },
    };

    /**
     * Mantiene el panel dentro del viewport recolocándolo si se ha salido por los bordes.
     */
    function clampPosition() {
        const rect = panel.getBoundingClientRect();
        const maxLeft = Math.max(MARGIN, window.innerWidth - rect.width - MARGIN);
        const maxTop = Math.max(MARGIN, window.innerHeight - rect.height - MARGIN);
        panel.style.left = Math.min(Math.max(MARGIN, rect.left), maxLeft) + 'px';
        panel.style.top = Math.min(Math.max(MARGIN, rect.top), maxTop) + 'px';
    }

    /**
     * Carga perezosa del iframe: asigna el src la primera vez que hace falta mostrarlo.
     */
    function cargarIframe() {
        if (!iframe.src && iframe.dataset.src) {
            iframe.src = iframe.dataset.src;
        }
    }

    /**
     * Pasa del anclado CSS (right/bottom) a coordenadas explícitas left/top para poder arrastrar.
     */
    function fijarCoordenadas() {
        const rect = panel.getBoundingClientRect();
        panel.style.right = 'auto';
        panel.style.bottom = 'auto';
        panel.style.width = rect.width + 'px';
        // Minimizado conserva la altura automática (solo cabecera); fijarla con bottom liberado lo estiraría hacia abajo vacío
        if (!panel.classList.contains('is-minimized')) {
            panel.style.height = rect.height + 'px';
        }
        panel.style.left = rect.left + 'px';
        panel.style.top = rect.top + 'px';
        clampPosition();
    }

    /**
     * Sincroniza el icono y los textos del botón según el estado (minimizar vs restaurar).
     */
    function actualizarBotonMinimizar(minimizado) {
        if (minimizeIcon) {
            minimizeIcon.classList.toggle('fa-minus', !minimizado);
            minimizeIcon.classList.toggle('fa-window-restore', minimizado);
        }

        if (minimizeBtn) {
            const label = minimizado ? minimizeBtn.dataset.labelRestaurar : minimizeBtn.dataset.labelMinimizar;
            minimizeBtn.setAttribute('aria-label', label);
            minimizeBtn.dataset.popup = label;
        }
    }

    /**
     * Minimiza el panel: lo reancla a la esquina inferior derecha (CSS) dejando visible solo la cabecera.
     */
    function minimizar() {
        ['left', 'top', 'right', 'bottom', 'width', 'height'].forEach((prop) => {
            panel.style[prop] = '';
        });
        panel.classList.add('is-minimized');
        actualizarBotonMinimizar(true);
    }

    /**
     * Restaura el panel a tamaño completo: carga el iframe y vuelve a fijar coordenadas para poder arrastrar.
     */
    function restaurar() {
        panel.classList.remove('is-minimized');
        cargarIframe();
        fijarCoordenadas();
        actualizarBotonMinimizar(false);
    }

    if (minimizeBtn) {
        minimizeBtn.addEventListener('click', () => {
            if (panel.classList.contains('is-minimized')) {
                restaurar();
            } else {
                minimizar();
            }
        });
    }

    /**
     * Resalta el preset activo (y lo quita del resto) o limpia todos cuando se redimensiona a mano.
     */
    function marcarPresetActivo(btnActivo) {
        sizeButtons.forEach((btn) => {
            btn.classList.toggle('is-active', btn === btnActivo);
        });
    }

    /**
     * Ajusta el panel a un tamaño concreto, limitándolo al viewport, y lo reencuadra dentro de la pantalla.
     */
    function aplicarSize(w, h) {
        const maxW = window.innerWidth - 2 * MARGIN;
        const maxH = window.innerHeight - 2 * MARGIN;
        panel.style.width = Math.max(MIN_W, Math.min(w, maxW)) + 'px';
        panel.style.height = Math.max(MIN_H, Math.min(h, maxH)) + 'px';
        clampPosition();
    }

    sizeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const preset = SIZE_PRESETS[btn.dataset.previewSize];
            if (!preset) {
                return;
            }

            aplicarSize(preset.w, preset.h);
            marcarPresetActivo(btn);
        });
    });

    // Voltear: intercambia el ancho actual por el alto (orientación horizontal/vertical); deja de haber preset activo
    if (flipBtn) {
        flipBtn.addEventListener('click', () => {
            const rect = panel.getBoundingClientRect();
            aplicarSize(rect.height, rect.width);
            marcarPresetActivo(null);
        });
    }

    // Arrastre del panel agarrando la cabecera (ignorando los botones/enlaces de acciones)
    let dragging = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let dragStartLeft = 0;
    let dragStartTop = 0;

    dragHandle.addEventListener('pointerdown', (e) => {
        if (e.target.closest('.pagina-preview-btn')) {
            return;
        }

        dragging = true;
        // Antes de mover pasamos a coordenadas explícitas: si seguimos anclados por CSS (caso minimizado), fijar top con bottom puesto estiraría el panel
        fijarCoordenadas();
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        const rect = panel.getBoundingClientRect();
        dragStartLeft = rect.left;
        dragStartTop = rect.top;
        // La captura redirige los eventos al handle aunque el puntero pase sobre el iframe
        dragHandle.setPointerCapture(e.pointerId);
        e.preventDefault();
    });

    dragHandle.addEventListener('pointermove', (e) => {
        if (!dragging) {
            return;
        }

        panel.style.left = dragStartLeft + (e.clientX - dragStartX) + 'px';
        panel.style.top = dragStartTop + (e.clientY - dragStartY) + 'px';
        clampPosition();
    });

    /**
     * Finaliza el arrastre y libera la captura del puntero.
     */
    function endDrag(e) {
        if (!dragging) {
            return;
        }

        dragging = false;
        try {
            dragHandle.releasePointerCapture(e.pointerId);
        } catch {
            // El puntero ya estaba liberado
        }
    }

    dragHandle.addEventListener('pointerup', endDrag);
    dragHandle.addEventListener('pointercancel', endDrag);

    // Redimensionado desde la esquina inferior derecha
    let resizing = false;
    let resizeStartX = 0;
    let resizeStartY = 0;
    let resizeStartW = 0;
    let resizeStartH = 0;

    resizer.addEventListener('pointerdown', (e) => {
        resizing = true;
        resizeStartX = e.clientX;
        resizeStartY = e.clientY;
        const rect = panel.getBoundingClientRect();
        resizeStartW = rect.width;
        resizeStartH = rect.height;
        // Al redimensionar manualmente ya no hay un preset de dispositivo activo
        marcarPresetActivo(null);
        resizer.setPointerCapture(e.pointerId);
        e.preventDefault();
    });

    resizer.addEventListener('pointermove', (e) => {
        if (!resizing) {
            return;
        }

        const rect = panel.getBoundingClientRect();
        const w = resizeStartW + (e.clientX - resizeStartX);
        const h = resizeStartH + (e.clientY - resizeStartY);
        panel.style.width = Math.max(MIN_W, Math.min(w, window.innerWidth - rect.left - MARGIN)) + 'px';
        panel.style.height = Math.max(MIN_H, Math.min(h, window.innerHeight - rect.top - MARGIN)) + 'px';
    });

    /**
     * Finaliza el redimensionado y libera la captura del puntero.
     */
    function endResize(e) {
        if (!resizing) {
            return;
        }

        resizing = false;
        try {
            resizer.releasePointerCapture(e.pointerId);
        } catch {
            // El puntero ya estaba liberado
        }
    }

    resizer.addEventListener('pointerup', endResize);
    resizer.addEventListener('pointercancel', endResize);

    // Al cambiar el tamaño de la ventana, reencuadramos el panel; minimizado se mantiene anclado por CSS, no se toca
    window.addEventListener('resize', () => {
        if (!panel.classList.contains('is-minimized')) {
            clampPosition();
        }
    });

    // Estado inicial según el render del servidor (clase is-minimized = $mostrarExtendido false): minimizado deja la carga del iframe perezosa;
    // extendido carga el iframe y fija coordenadas para poder arrastrar desde el primer momento
    if (!panel.classList.contains('is-minimized')) {
        cargarIframe();
        fijarCoordenadas();
    }
}
