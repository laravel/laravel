import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    // Load env file based on `mode` (dev, serve or build) in the current working directory.
    // Set the 3rd parameter to '' to load all env variables regardless of the `VITE_` prefix.
    const env = loadEnv(mode, process.cwd(), '')
    const host = env.APP_URL.match(/\/\/([\w.]+)/)[1]

    return {
        server: {
            hmr: {
                host,
            },
        },
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
        ],
    }
});
