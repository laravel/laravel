<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  2.0.0
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

/*
|--------------------------------------------------------------------------
| Active Modules
|--------------------------------------------------------------------------
|
| Modules are a convenient way to organize your application into logical
| components. Each module may have its own libraries, models, routes,
| views, language files, and configuration.
|
| Here you may specify which modules are active for your application.
| This simply gives Laravel an easy way to know which directories to
| check when auto-loading your classes, routes, and views.
|
*/

$active = array();

/*
|--------------------------------------------------------------------------
| Installation Paths
|--------------------------------------------------------------------------
|
| Here you may specify the location of the various Laravel framework
| directories for your installation. 
|
| Of course, these are already set to the proper default values, so you do
| not need to change them if you have not modified the directory structure.
|
*/

$application = '../application';

$laravel     = '../laravel';

$packages    = '../packages';

$modules     = '../modules';

$storage     = '../storage';

$public      = __DIR__;

/*
|--------------------------------------------------------------------------
| 3... 2... 1... Lift-off!
|--------------------------------------------------------------------------
*/
require $laravel.'/laravel.php';