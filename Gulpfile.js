var elixir = require('./vendor/laravel/elixir/Elixir');

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
    mix.sass("bootstrap.scss")
       .routes()
       .events()
       .phpUnit();
});
