import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],
    server: {
        host: '0.0.0.0', // Permite conexões externas
        port: 5173, // Garante que a porta correta seja usada
        hmr: {
            host: 'localhost', // Substitua por seu endereço IP público, se necessário
        },
    },
});
