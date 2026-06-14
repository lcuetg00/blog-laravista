import { getCookie, setCookie } from './cookies.js';

// Constantes para la cookie del tema (igual que public.js)
const THEME_COOKIE_NAME = 'theme';
const THEME_COOKIE_EXPIRY_DAYS = 3650;

/**
 * Detecta la preferencia de tema del sistema operativo (dark o light).
 */
function detectSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

/**
 * Inicializa el tema desde la cookie guardada o, si no hay, desde la preferencia del sistema.
 */
export function initTheme() {
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

/**
 * Actualiza el icono del selector de tema según el modo activo (luna/sol).
 */
function updateThemeIcon(theme) {
    const icon = document.getElementById('modo-seleccionado');
    if (icon) {
        icon.className = theme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    }
}

/**
 * Cambia entre modo claro (1) y oscuro (2) y lo persiste en cookie (llamada desde la vista Blade).
 */
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
