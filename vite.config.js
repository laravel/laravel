import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                'resources/assets/scss/app.scss',
                'resources/assets/scss/bootstrap.scss',
                'resources/assets/scss/icons.scss',

                'resources/assets/js/app.js',
                'resources/assets/js/pages/index.init.js',
                'resources/assets/js/pages/password-addon.init.js',
                'resources/assets/js/pages/theme-style.init.js',
                'resources/assets/js/pages/validation.init.js',

                'resources/assets/libs/bootstrap/bootstrap.js',
                'resources/assets/libs/fg-emoji-picker/fgEmojiPicker.js',
                'resources/assets/libs/glightbox/glightbox.js',
                'resources/assets/libs/node-waves/waves.js',
                'resources/assets/libs/simplebar/simplebar.js',
                'resources/assets/libs/swiper/swiper-bundle.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Http/Livewire/**',
            ],
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/assets',
            '~': '/node_modules',
        }
    },
});
