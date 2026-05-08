/* globals Chart */

// Constantes para la cookie del tema (igual que public.js)
const THEME_COOKIE_NAME = 'theme';
const THEME_COOKIE_EXPIRY_DAYS = 3650;

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

// Inicializar gráfico del dashboard si existe el canvas
function initDashboardChart() {
    const ctx = document.getElementById('myChart');
    if (!ctx || typeof Chart === 'undefined') return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            datasets: [{
                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                borderWidth: 4,
                pointBackgroundColor: '#007bff',
            }],
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { boxPadding: 3 },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initDashboardChart();
});
