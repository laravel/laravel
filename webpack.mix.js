const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

/*
 |--------------------------------------------------------------------------
 | Client Code
 |--------------------------------------------------------------------------
 */
mix.react('resources/client/js/client.js', 'public/client/js')
   .sass('resources/client/sass/client.scss', 'public/client/css');

mix.options({
  hmrOptions: {
    host: process.env.MIX_HOT_MODULE_REPLACEMENT_HOST,
    port: process.env.MIX_HOT_MODULE_REPLACEMENT_PORT,
  }
});
