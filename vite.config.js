import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // 'node_modules/@coreui/icons/sprites',
            ],
            refresh: true,
            sprites: {
                output: 'public/svg-icons',
                format: 'svg',
            },
        }),
    ],
    resolve: {
        alias: {
          '@coreui/icons/sprites': path.resolve(__dirname, 'node_modules/@coreui/icons/sprites'),
        },
      },
});
