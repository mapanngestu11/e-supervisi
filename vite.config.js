import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/guru.js', 'resources/js/instrumen.js', 'resources/js/laporan.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
