// Importar Swiper y módulos necesarios
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectCoverflow, FreeMode } from 'swiper/modules';

// Constantes para la cookie del tema
const THEME_COOKIE_NAME = 'theme';
const THEME_COOKIE_EXPIRY_DAYS = 3650; // 10 años (duración indefinida)

/**
 * Obtiene el valor de una cookie por nombre
 */
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

/**
 * Establece una cookie
 */
function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = `expires=${date.toUTCString()}`;
    document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
}

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

    setCookie(THEME_COOKIE_NAME, theme, THEME_COOKIE_EXPIRY_DAYS);
    updateThemeIcon(theme);
}

// Para que el html pueda acceder a ella, porque la importamos con vite
window.changePageMode = changePageMode;

// Detectar cambios en la preferencia del sistema
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

/**
 * Inicializa los carruseles de tecnologías con movimiento continuo
 * DESHABILITADO: Ahora se usa CSS animations puras para evitar saltos en el loop
 */
// function initTechCarousels() {
//     const frontendContainer = document.querySelector('.swiper-frontend');
//     const herramientasContainer = document.querySelector('.swiper-herramientas');

//     if (frontendContainer) {
//         new Swiper('.swiper-frontend', {
//             modules: [Autoplay, FreeMode],
//             slidesPerView: 'auto',
//             spaceBetween: 30,
//             loop: true,
//             loopedSlides: 6,
//             speed: 8000,
//             freeMode: {
//                 enabled: true,
//                 momentum: false,
//             },
//             autoplay: {
//                 delay: 0,
//                 disableOnInteraction: false,
//                 pauseOnMouseEnter: false,
//             },
//             breakpoints: {
//                 576: {
//                     spaceBetween: 30,
//                 },
//                 768: {
//                     spaceBetween: 40,
//                 },
//                 992: {
//                     spaceBetween: 50,
//                 },
//             },
//         });
//     }

//     if (herramientasContainer) {
//         new Swiper('.swiper-herramientas', {
//             modules: [Autoplay, FreeMode],
//             slidesPerView: 'auto',
//             spaceBetween: 30,
//             loop: true,
//             loopedSlides: 6,
//             speed: 8000,
//             freeMode: {
//                 enabled: true,
//                 momentum: false,
//             },
//             autoplay: {
//                 delay: 0,
//                 disableOnInteraction: false,
//                 pauseOnMouseEnter: false,
//             },
//             breakpoints: {
//                 576: {
//                     spaceBetween: 30,
//                 },
//                 768: {
//                     spaceBetween: 40,
//                 },
//                 992: {
//                     spaceBetween: 50,
//                 },
//             },
//         });
//     }
// }

// Inicializar el tema antes de que se cargue la página para evitar parpadeo
initTheme();

// Inicializar los carruseles cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    initCarousel();
    // initTechCarousels(); // Deshabilitado - ahora se usa CSS animations
});