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
    mix
        .copy(
        [
            'node_modules/jquery/dist/jquery.js',
            'node_modules/bootstrap-sass/assets/javascripts/bootstrap.js'
        ], './resources/assets/js/vendor')

		.scripts(
		[
            'vendor/jquery.js',
	        'vendor/bootstrap.js',
	    ], 'public/dist/js/bundle.js')

        .sass('import.scss', 'public/dist/css/styles.css')

        .version([
            'dist/js/bundle.js',
            'dist/css/styles.css'
        ])
        
        .livereload();
});