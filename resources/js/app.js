// Iconos Fontawesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// Bootstrap CSS
import 'bootstrap/dist/css/bootstrap.min.css';

// Bootstrap JS
// Centralizamos Bootstrap en window porque app.js y panel.js se cargan juntos en el panel, si cada bundle hiciera su propio import, habría dos copias.
// Usando 'bootstrap/dist/js/bootstrap.bundle.min.js' no garantiza que exista window.bootstrap, por lo que lo importamos a mano y lo asignamos
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
