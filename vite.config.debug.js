import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Explicitly set the output directory for manifest.json
            buildDirectory: 'build',
        }),
    ],
    build: {
        // Add verbose output for debugging
        minify: false,
        manifest: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
        outDir: 'public/build',
    },
    // Log more details during build
    logLevel: 'info',
});
