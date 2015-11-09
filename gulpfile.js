var gulp = require('gulp');
var elixir = require('laravel-elixir');

/*-----------------------------------------
 | Task 'gulp' will error
 | -  A recent update broke this process, don't know what but running the task gulp will error
 | 
 | Task 'gulp watch' will work fine
 | - A recent update broke the gulp task, whereas watch still works fine
 | ----------------------------------------
 */


/*-----------------------------------------
 | Custom Gulp Tasks
 |
 | -  Browserify already watches/runs by default so we don't need to double up
 | ----------------------------------------
 */
gulp.task('buildAssets', ['sass', 'version']);

elixir(function (mix) {

    /*
     *  TO DO: use npm to import vendor dependencies directly via browserify (will remove the need for a .copy task for js files
     */
    mix
        /*-----------------------------------------
         | Public site (www)
         | ----------------------------------------
         */
        .sass('../../assets/www/sass/import.scss', 'public/dist/www/css/styles.css')
        .browserify('../../assets/www/js/main.js', 'public/dist/www/js/bundle.js')
        .copy('node_modules/font-awesome/fonts', 'public/dist/www/fonts/font-awesome')

        /*-----------------------------------------
         | Admin site (cms) [ NOT SETUP YET ]
         | ----------------------------------------
         */
//        .sass('../../assets/cms/sass/import.scss', 'public/dist/cms/css/styles.css')
//        .browserify('../../assets/cms/js/main.js', 'public/dist/cms/js/bundle.js')


        /*-----------------------------------------
         | Versions
         | ----------------------------------------
         */
        .version([
//            'dist/cms/js/bundle.js',
//            'dist/cms/css/styles.css',
            'dist/www/js/bundle.js',
            'dist/www/css/styles.css'
        ])


        /*-----------------------------------------
         | Watcher (Not really, but maybe?)
         | ----------------------------------------
         */
        .task('buildAssets', 'resources/assets/**');
});