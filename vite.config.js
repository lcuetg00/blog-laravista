import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/carousel.css', 'resources/js/app.js',  'resources/css/public.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
