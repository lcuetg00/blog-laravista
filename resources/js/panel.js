import Alpine from 'alpinejs';

// Exponemos Alpine en window para poder depurarlo desde la consola y arrancamos su loop
window.Alpine = Alpine;
Alpine.start();

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

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initToasts();
});
