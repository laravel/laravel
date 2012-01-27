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
// Define the directory separator for the environment.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require 'paths.php';

// --------------------------------------------------------------
// Override the application paths when testing the core.
// --------------------------------------------------------------
$config = file_get_contents('phpunit.xml');

if (strpos($config, 'laravel-tests') !== false)
{
	$path = $GLOBALS['BUNDLE_PATH'].'laravel-tests'.DS;

	$GLOBALS['APP_PATH'] = $path.'application'.DS;

	$GLOBALS['BUNDLE_PATH'] = $path.'bundles'.DS;

	$GLOBALS['STORAGE_PATH'] = $path.'storage'.DS;
}

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require $GLOBALS['SYS_PATH'].'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Bundle::start(DEFAULT_BUNDLE);