import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        rollupOptions: {
            input: ['styles.css', 'scripts.js'],
            output: {
                assetFileNames: '[name][extname]',
                entryFileNames: '[name].js',
            },
        },
    },
});
