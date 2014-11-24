var elixir = require('laravel-elixir');

/*
 |----------------------------------------------------------------
 | Have a Drink!
 |----------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic
 | Gulp tasks for your Laravel application. Elixir supports
 | several common CSS, JavaScript and even testing tools!
 |
 */

elixir(function(mix) {
    mix.sass('app.scss')
       .publish(
            'jquery/dist/jquery.min.js',
            'public/js/vendor/jquery.js'
        )
       .publish(
            'bootstrap-sass-official/assets/javascripts/bootstrap.js',
            'public/js/vendor/bootstrap.js'
        )
       .publish(
            'font-awesome/css/font-awesome.min.css',
            'public/css/vendor/font-awesome.css'
        )
       .publish(
            'font-awesome/fonts',
            'public/css/vendor/fonts'
        );
});
