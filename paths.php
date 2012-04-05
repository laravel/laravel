<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  3.1.6
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Initialize the web variable if it doesn't exist.
// --------------------------------------------------------------
if ( ! isset($web)) $web = false;

// --------------------------------------------------------------
// Change to the current directory if not from the web.
// --------------------------------------------------------------
if ( ! $web)
{
	chdir(__DIR__);
}

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
$GLOBALS['laravel_paths']['base'] = __DIR__.DS;

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
$paths['app'] = 'application';

// --------------------------------------------------------------
// The path to the Laravel directory.
// --------------------------------------------------------------
$paths['sys'] = 'laravel';

// --------------------------------------------------------------
// The path to the bundles directory.
// --------------------------------------------------------------
$paths['bundle'] = 'bundles';

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
$paths['storage'] = 'storage';

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
if ($web)
{
	$GLOBALS['laravel_paths']['public'] = realpath('').DS;
}
else
{
	$paths['public'] = 'public';
}

// --------------------------------------------------------------
// Define each constant if it hasn't been defined.
// --------------------------------------------------------------
foreach ($paths as $name => $path)
{
	if ($web) $path = "../{$path}";

	if ( ! isset($GLOBALS['laravel_paths'][$name]))
	{
		$GLOBALS['laravel_paths'][$name] = realpath($path).DS;
	}
}

/**
 * A global path helper function.
 * 
 * <code>
 *     $storage = path('storage');
 * </code>
 * 
 * @param  string  $path
 * @return string
 */
function path($path)
{
	return $GLOBALS['laravel_paths'][$path];
}

/**
 * A global path setter function.
 * 
 * @param  string  $path
 * @param  string  $value
 * @return void
 */
function set_path($path, $value)
{
	$GLOBALS['laravel_paths'][$path] = $value;
}