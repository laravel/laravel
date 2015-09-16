var elixir = require('laravel-elixir');
require('laravel-elixir-livereload');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {

    //TODO: use npm to import vendor dependencies directly via browserify (will remove the need for a .copy task for js files
    mix
        /*-----------------------------------------
         | Public (www) site
         | ----------------------------------------
         */

        .browserify('../../www/assets/js/app.js', 'public/www/dist/js/bundle.js')

        .sass('../../www/assets/sass/import.scss', 'public/www/dist/css/styles.css')

        .version([
            'www/dist/js/bundle.js',
            'www/dist/css/styles.css'
        ], 'public/www');

        /*-----------------------------------------
         | Admin (cms) site
         | ----------------------------------------
         */
        
});