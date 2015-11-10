var gulp = require('gulp');
var elixir = require('laravel-elixir');

elixir(function (mix) {
    mix
        /*-----------------------------------------
         | Public site (www)
         | ----------------------------------------
         */
        .sass('import.scss', './public/dist/www/css/styles.css')
        .browserify('www/js/main.js', './public/dist/www/js/bundle.js')
        

        /*-----------------------------------------
         | Admin site (cms)
         | ----------------------------------------
         */
        .sass('import.scss', './public/dist/cms/css/styles.css')
        .browserify('cms/js/main.js', './public/dist/cms/js/bundle.js')


        /*-----------------------------------------
         | Copy
         | ----------------------------------------
         */
        .copy('node_modules/font-awesome/fonts', '/dist/www/fonts/font-awesome')


        /*-----------------------------------------
         | Version
         | ----------------------------------------
         */
        .version([
            '/public/dist/cms/js/bundle.js',
            '/public/dist/cms/css/styles.css',
            './public/dist/www/js/bundle.js',
            './public/dist/www/css/styles.css'
        ])
});