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

/*
|--------------------------------------------------------------------------
| Laravel Installation Paths
|--------------------------------------------------------------------------
*/

$application = '../application';

$laravel     = '../laravel';

$storage     = '../storage';

$public      = __DIR__;

/*
|--------------------------------------------------------------------------
| 3... 2... 1... Lift-off!
|--------------------------------------------------------------------------
*/

require $laravel.'/laravel.php';

echo number_format((microtime(true) - START_TIME) * 1000, 2);