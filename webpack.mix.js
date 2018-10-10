let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');


/*
|-----------------------------------------------------------------------
| BrowserSync
|-----------------------------------------------------------------------
|
| BrowserSync refreshes the browser if file changes (js, sass, blade.php) are
| detected.
|
| For more information: https://browsersync.io/docs
*/
mix.browserSync({
  /*
   * Specify the URL that addresses your server.
   * This can also be an IP or your homestead environment.
   */
  proxy: 'http://localhost:8000',

  /*
   * Host name for external use. Here you can also specify a domain, that you have
   * edited in your local hosts file. Most of the time this can be untouched.
   */
  host: 'localhost',

  /*
   * Open the browser. You can set this to false if you are using a vagrant development
   * environment like homestead.
   */
  open: true,

  /*
   * Use polling if your are using the homestead environment or an equivalent
   * vagrant development environment.
   */
  watchOptions: {
    usePolling: false
  }
});