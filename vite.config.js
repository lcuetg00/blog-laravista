import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',  
                'resources/css/public.css',
                'resources/js/public.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    optimizeDeps: {
        // Aquí va todo lo que es javascript, para que se optimice
        include: [
            'bootstrap', // Solo javascript
            '@fortawesome/fontawesome-free/js/all.js',
        ],
     },
});
