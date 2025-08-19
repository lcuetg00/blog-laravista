/**
 * Cambia a modo claro/oscuro con un simple int. Definido en las opciones del desplegable
 * 
 * @param {*} value 
 */
function changePageMode(value) {
    let html = document.documentElement;

    if(value === 1) {
        html.setAttribute('data-bs-theme', 'light');
    }

    if(value === 2) {
        html.setAttribute('data-bs-theme', 'dark');
    }
}

// Para que el html pueda acceder a ella, porque la importamos con vite
window.changePageMode = changePageMode;