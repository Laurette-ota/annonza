import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/annonces/index.js',
                'resources/js/annonces/create.js',
                'resources/js/annonces/show.js',
                'resources/js/annonces/edit.js',
                'resources/js/categories/index.js',
                'resources/js/categories/show.js',
                'resources/js/favorites/index.js',
            ],
            refresh: true,
        }),
    ],
});
