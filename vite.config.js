import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Specify the manifest should go in the root build directory
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                // Ensure manifest.json is placed in public/build/ instead of public/build/.vite/
                manifest: 'manifest.json',
            },
        },
    },
});
