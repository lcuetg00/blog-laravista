// Importar Swiper y módulos necesarios
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectCoverflow } from 'swiper/modules';

/**
 * Detecta la preferencia de tema del sistema operativo
 */
function detectSystemTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    return 'light';
}

/**
 * Inicializa el tema basándose en la preferencia guardada o del sistema
 */
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    const html = document.documentElement;

    if (savedTheme) {
        html.setAttribute('data-bs-theme', savedTheme);
        updateThemeIcon(savedTheme);
    } else {
        const systemTheme = detectSystemTheme();
        html.setAttribute('data-bs-theme', systemTheme);
        updateThemeIcon(systemTheme);
    }
}

/**
 * Actualiza el icono del selector de tema
 */
function updateThemeIcon(theme) {
    const icon = document.getElementById('modo-seleccionado');
    if (icon) {
        if (theme === 'dark') {
            icon.className = 'fa-solid fa-moon';
        } else {
            icon.className = 'fa-solid fa-sun';
        }
    }
}

/**
 * Cambia a modo claro/oscuro con un simple int. Definido en las opciones del desplegable
 *
 * @param {*} value
 */
function changePageMode(value) {
    let html = document.documentElement;
    let theme = '';

    if(value === 1) {
        theme = 'light';
        html.setAttribute('data-bs-theme', 'light');
    }

    if(value === 2) {
        theme = 'dark';
        html.setAttribute('data-bs-theme', 'dark');
    }

    localStorage.setItem('theme', theme);
    updateThemeIcon(theme);
}

// Para que el html pueda acceder a ella, porque la importamos con vite
window.changePageMode = changePageMode;

// Detectar cambios en la preferencia del sistema
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const savedTheme = localStorage.getItem('theme');
        if (!savedTheme) {
            const newTheme = e.matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            updateThemeIcon(newTheme);
        }
    });
}

/**
 * Inicializa el carrusel 3D con Swiper
 */
function initCarousel() {
    const swiperContainer = document.querySelector('.swiper-carousel');
    if (!swiperContainer) return;

    new Swiper('.swiper-carousel', {
        modules: [Navigation, Pagination, Autoplay, EffectCoverflow],
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        speed: 1200,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
}

// Inicializar el tema antes de que se cargue la página para evitar parpadeo
initTheme();

// Inicializar el carrusel cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initCarousel);