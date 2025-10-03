import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
    ],

    theme: {
       extend: {
            colors: {
                primary: '#1D4ED8', // Blue
                secondary: '#F3F4F6', // Light Gray
                accent: '#FBBF24', // Yellow
            },
            fontFamily: {
                roboto: ['Roboto', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
