// Iconos Fontawesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// Bootstrap CSS
import 'bootstrap/dist/css/bootstrap.min.css';

// Bootstrap JS
// Centralizamos Bootstrap en window porque app.js y panel.js se cargan juntos en el panel, si cada bundle hiciera su propio import, habría dos copias.
// Usando 'bootstrap/dist/js/bootstrap.bundle.min.js' no garantiza que exista window.bootstrap, por lo que lo importamos a mano y lo asignamos
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Bundling manual de Livewire (doc oficial: "Manually bundling Livewire and Alpine"): usamos el Alpine que ya trae Livewire (un único Alpine).
// Va en app.js (no en panel.js) para que Alpine esté disponible en panel, login y público. Requiere 'inject_assets' => false en config/livewire.php
// y @livewireScriptConfig en el <head> de los tres layouts que cargan este bundle.
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Exponemos Alpine y Livewire en window: Alpine para depurar desde consola y 
// Livewire para que panel.js (otro bundle) pueda registrar listeners de sus eventos (toast, recargar-preview)
window.Alpine = Alpine;
window.Livewire = Livewire;
Livewire.start();
