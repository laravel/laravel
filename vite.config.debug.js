import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            buildDirectory: 'build',
            publicDirectory: 'public',
        }),
    ],
    build: {
        // Add verbose output for debugging
        minify: false,
        manifest: true,
        emptyOutDir: true,
        outDir: resolve(__dirname, 'public/build'),
        assetsDir: '',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    // Log more details during build
    logLevel: 'info',
});
