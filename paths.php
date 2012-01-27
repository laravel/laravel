<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  2.2.0 (Beta 1)
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Initialize the web variable if it doesn't exist.
// --------------------------------------------------------------
if ( ! isset($web)) $web = false;

// --------------------------------------------------------------
// Define the directory separator for the environment.
// --------------------------------------------------------------
if ( ! defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

// --------------------------------------------------------------
// Define the path to the base directory.
// --------------------------------------------------------------
$GLOBALS['BASE_PATH'] = __DIR__.DS;

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
$paths['APP_PATH'] = 'application';

// --------------------------------------------------------------
// The path to the Laravel directory.
// --------------------------------------------------------------
$paths['SYS_PATH'] = 'laravel';

// --------------------------------------------------------------
// The path to the bundles directory.
// --------------------------------------------------------------
$paths['BUNDLE_PATH'] = 'bundles';

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
$paths['STORAGE_PATH'] = 'storage';

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
if ($web)
{
	$GLOBALS['PUBLIC_PATH'] = realpath('').DS;
}
else
{
	$paths['PUBLIC'] = 'public';
}

// --------------------------------------------------------------
// Define each constant if it hasn't been defined.
// --------------------------------------------------------------
foreach ($paths as $name => $path)
{
	if ($web) $path = "../{$path}";

	$GLOBALS[$name] = realpath($path).DS;
}