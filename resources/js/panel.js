// Constantes para la cookie del tema (igual que public.js)
const THEME_COOKIE_NAME = 'theme';
const THEME_COOKIE_EXPIRY_DAYS = 3650;

// Cookie que persiste el estado colapsado/expandido del sidebar del panel
const SIDEBAR_COOKIE_NAME = 'panel_sidebar_collapsed';
const SIDEBAR_COOKIE_EXPIRY_DAYS = 3650;

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
}

function detectSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

function initTheme() {
    const savedTheme = getCookie(THEME_COOKIE_NAME);
    const html = document.documentElement;

    if (savedTheme) {
        html.setAttribute('data-bs-theme', savedTheme);
        updateThemeIcon(savedTheme);
    } else {
        const systemTheme = detectSystemTheme();
        html.setAttribute('data-bs-theme', systemTheme);
        updateThemeIcon(systemTheme);
        setCookie(THEME_COOKIE_NAME, systemTheme, THEME_COOKIE_EXPIRY_DAYS);
    }
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('modo-seleccionado');
    if (icon) {
        icon.className = theme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    }
}

function changePageMode(value) {
    const html = document.documentElement;
    let theme = '';

    if (value === 1) {
        theme = 'light';
        html.setAttribute('data-bs-theme', 'light');
    }

    if (value === 2) {
        theme = 'dark';
        html.setAttribute('data-bs-theme', 'dark');
    }

    setCookie(THEME_COOKIE_NAME, theme, THEME_COOKIE_EXPIRY_DAYS);
    updateThemeIcon(theme);
}

// Expuesto globalmente para usarlo desde el HTML de la vista Blade
window.changePageMode = changePageMode;

// Detectar cambios de preferencia del sistema
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const savedTheme = getCookie(THEME_COOKIE_NAME);
        if (!savedTheme) {
            const newTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            updateThemeIcon(newTheme);
            setCookie(THEME_COOKIE_NAME, newTheme, THEME_COOKIE_EXPIRY_DAYS);
        }
    });
}

// Sidebar del panel: colapsar/expandir en escritorio con persistencia en cookie
const SIDEBAR_DESKTOP_MQ = window.matchMedia('(min-width: 768px)');

function isSidebarCollapsed() {
    return document.body.classList.contains('sidebar-collapsed');
}

function applySidebarState(collapsed, btn) {
    if (!SIDEBAR_DESKTOP_MQ.matches) {
        // En móvil siempre expandido (el offcanvas se gestiona aparte)
        document.body.classList.remove('sidebar-collapsed');
        return;
    }

    document.body.classList.toggle('sidebar-collapsed', collapsed);

    if (btn) {
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        btn.setAttribute(
            'aria-label',
            collapsed
                ? btn.dataset.labelExpand || 'Expand sidebar'
                : btn.dataset.labelCollapse || 'Collapse sidebar',
        );
        const icon = btn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-angles-left', !collapsed);
            icon.classList.toggle('fa-angles-right', collapsed);
        }
    }
}

function initSidebar() {
    const btn = document.getElementById('sidebarCollapseBtn');
    if (!btn) {
        return;
    }

    const stored = getCookie(SIDEBAR_COOKIE_NAME) === '1';
    applySidebarState(stored, btn);

    btn.addEventListener('click', () => {
        const next = !isSidebarCollapsed();
        setCookie(SIDEBAR_COOKIE_NAME, next ? '1' : '0', SIDEBAR_COOKIE_EXPIRY_DAYS);
        applySidebarState(next, btn);
    });

    // Si se cruza el breakpoint, reevaluar para no dejar tooltips colgando
    SIDEBAR_DESKTOP_MQ.addEventListener('change', () => {
        applySidebarState(getCookie(SIDEBAR_COOKIE_NAME) === '1', btn);
    });
}

// Inicializa todos los toasts del DOM y los muestra al cargar la página
function initToasts() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
        return;
    }

    const toastEls = document.querySelectorAll('.toast');
    toastEls.forEach((el) => {
        const toast = bootstrap.Toast.getOrCreateInstance(el);
        toast.show();
    });
}

// Panel flotante de vista previa (show de Página): arrastrable y redimensionable con Pointer Events (ratón y táctil unificados)
function initPreviewModal() {
    const toggle = document.getElementById('paginaPreviewToggle');
    const panel = document.getElementById('paginaPreview');
    if (!toggle || !panel) {
        return;
    }

    const iframe = panel.querySelector('iframe');
    const dragHandle = panel.querySelector('[data-preview-drag]');
    const resizer = panel.querySelector('[data-preview-resize]');
    const closeBtn = panel.querySelector('[data-preview-close]');
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

    // Mantiene el panel dentro del viewport recolocándolo si se ha salido por los bordes
    function clampPosition() {
        const rect = panel.getBoundingClientRect();
        const maxLeft = Math.max(MARGIN, window.innerWidth - rect.width - MARGIN);
        const maxTop = Math.max(MARGIN, window.innerHeight - rect.height - MARGIN);
        panel.style.left = Math.min(Math.max(MARGIN, rect.left), maxLeft) + 'px';
        panel.style.top = Math.min(Math.max(MARGIN, rect.top), maxTop) + 'px';
    }

    function openPanel() {
        // Carga perezosa del iframe: asignamos el src la primera vez que se abre
        if (!iframe.src && iframe.dataset.src) {
            iframe.src = iframe.dataset.src;
        }

        panel.hidden = false;

        // Pasamos del anclado CSS (right/bottom) a coordenadas explícitas left/top para poder arrastrar
        const rect = panel.getBoundingClientRect();
        panel.style.right = 'auto';
        panel.style.bottom = 'auto';
        panel.style.width = rect.width + 'px';
        panel.style.height = rect.height + 'px';
        panel.style.left = rect.left + 'px';
        panel.style.top = rect.top + 'px';
        clampPosition();

        toggle.setAttribute('aria-expanded', 'true');
        panel.focus();
    }

    function closePanel() {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
    }

    toggle.addEventListener('click', () => {
        if (panel.hidden) {
            openPanel();
        } else {
            closePanel();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closePanel);
    }

    // Resalta el preset activo (y lo quita del resto) o limpia todos cuando se redimensiona a mano
    function marcarPresetActivo(btnActivo) {
        sizeButtons.forEach((btn) => {
            btn.classList.toggle('is-active', btn === btnActivo);
        });
    }

    // Ajusta el panel a un tamaño concreto, limitándolo al viewport, y lo reencuadra dentro de la pantalla
    function aplicarTamano(w, h) {
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

            aplicarTamano(preset.w, preset.h);
            marcarPresetActivo(btn);
        });
    });

    // Voltear: intercambia el ancho actual por el alto (orientación horizontal/vertical); deja de haber preset activo
    if (flipBtn) {
        flipBtn.addEventListener('click', () => {
            const rect = panel.getBoundingClientRect();
            aplicarTamano(rect.height, rect.width);
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

    // Al cambiar el tamaño de la ventana, reencuadramos el panel para que no quede fuera de pantalla
    window.addEventListener('resize', () => {
        if (!panel.hidden) {
            clampPosition();
        }
    });
}

// Popups CSS-only (.popup[data-popup]): si centrados se saldrían por un borde lateral, los anclamos a ese lado para que el texto quepa entero
function initPopups() {
    const popups = document.querySelectorAll('.popup[data-popup]');
    if (!popups.length) {
        return;
    }

    const MARGIN = 8;

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

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initToasts();
    initPreviewModal();
    initPopups();
});
