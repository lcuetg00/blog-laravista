import { getCookie, setCookie } from '../shared/cookies.js';

// Cookie que persiste el estado colapsado/expandido del sidebar del panel
const SIDEBAR_COOKIE_NAME = 'panel_sidebar_collapsed';
const SIDEBAR_COOKIE_EXPIRY_DAYS = 3650;

// Sidebar del panel: colapsar/expandir en escritorio con persistencia en cookie
const SIDEBAR_DESKTOP_MQ = window.matchMedia('(min-width: 768px)');

/**
 * Indica si el sidebar está actualmente colapsado.
 */
function isSidebarCollapsed() {
    return document.body.classList.contains('sidebar-collapsed');
}

/**
 * Aplica el estado colapsado/expandido al body y sincroniza el botón (icono, aria-label, aria-expanded).
 */
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

/**
 * Inicializa el sidebar: restaura el estado de la cookie, engancha el botón y reevalúa al cruzar el breakpoint.
 */
export function initSidebar() {
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
