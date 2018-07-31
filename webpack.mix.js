const mix = require('laravel-mix');

// Load the multi-lingual support
mix.extend('i18n', new (require('./build/laravel-mix-i18n'))());

mix
	.js('resources/assets/js/app.js', 'public/assets/js')
	.sass('resources/assets/sass/app.scss', 'public/assets/css');
