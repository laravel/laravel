<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  3.0.0
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
	$path = path('bundle').'laravel-tests'.DS;

	set_path('app', $path.'application'.DS);

	set_path('bundle', $path.'bundles'.DS);

	set_path('storage', $path.'storage'.DS);
}

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require path('sys').'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Bundle::start(DEFAULT_BUNDLE);