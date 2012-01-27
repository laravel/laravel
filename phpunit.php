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
// Override the framework paths if testing Laravel.
// --------------------------------------------------------------
if (in_array('build.xml', $_SERVER['argv']))
{
	define('APP_PATH', realpath('bundles/laravel-tests/application').DS);

	define('BUNDLE_PATH', realpath('bundles/laravel-tests/bundles').DS);

	define('STORAGE_PATH', realpath('bundles/laravel-tests/storage').DS);
}

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require 'paths.php';

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require SYS_PATH.'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Bundle::start(DEFAULT_BUNDLE);