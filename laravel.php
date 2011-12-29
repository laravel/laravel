<?php
/**
 * Laravel (CLI) - A Command Line For Web Artisans
 *
 * @package  Laravel
 * @version  2.0.7
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
define('APP_PATH', realpath('application').'/');

// --------------------------------------------------------------
// The path to the bundles directory.
// --------------------------------------------------------------
define('BUNDLE_PATH', realpath('bundles').'/');

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
define('STORAGE_PATH', realpath('storage').'/');

// --------------------------------------------------------------
// The path to the Laravel directory.
// --------------------------------------------------------------
define('SYS_PATH', realpath('laravel').'/');

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
define('PUBLIC_PATH', realpath('public').'/');

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require SYS_PATH.'core.php';

// --------------------------------------------------------------
// Create a CLI command implementation.
// --------------------------------------------------------------
$command = array_get($_SERVER['argv'], 1);

$command = Laravel\CLI\Commands\Factory::make($command);

// --------------------------------------------------------------
// If a valid command implementation was found, execute it.
// --------------------------------------------------------------
if ( ! is_null($command))
{
	$command->run(array_slice($_SERVER['argv'], 2));
}

// --------------------------------------------------------------
// Throw an extra line feed at the command line.
// --------------------------------------------------------------
echo PHP_EOL;