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

        .browserify('../../assets/www/js/main.js', 'public/dist/www/js/bundle.js')

        .sass('../../assets/www/sass/import.scss', 'public/dist/www/css/styles.css')

        .version([
            'dist/www/js/bundle.js',
            'dist/www/css/styles.css'
        ], 'public');

        /*-----------------------------------------
         | Admin (cms) site
         | ----------------------------------------
         */
        
});

/*
 |--------------------------------------------------------------------------
 | Elixir Extensions
 |--------------------------------------------------------------------------
 */

elixir.extend('buildJs', function () {
    gulp.task('build-js', function () {
        gulp.task(['browserify', 'version'])
    });

    this.registerWatcher('build-js', 'resources/**/assets/js/**');
    return this.queueTask('build-js');
});