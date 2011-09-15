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
| Tick... Tock... Tick... Tock
|--------------------------------------------------------------------------
*/
define('START_TIME', microtime(true));

function elapsed()
{
	return number_format((microtime(true) - START_TIME) * 1000, 2);
}

/*
|--------------------------------------------------------------------------
| Laravel Installation Paths
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

$storage     = '../storage';

$public      = __DIR__;

/*
|--------------------------------------------------------------------------
| 3... 2... 1... Lift-off!
|--------------------------------------------------------------------------
*/
require $laravel.'/laravel.php';
