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
// Tick... Tock... Tick... Tock...
// --------------------------------------------------------------
define('LARAVEL_START', microtime(true));

// --------------------------------------------------------------
// Define the directory separator for the environment.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
define('APP_PATH', realpath('../application').DS);

// --------------------------------------------------------------
// The path to the bundles directory.
// --------------------------------------------------------------
define('BUNDLE_PATH', realpath('../bundles').DS);

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
define('STORAGE_PATH', realpath('../storage').DS);

// --------------------------------------------------------------
// The path to the Laravel directory.
// --------------------------------------------------------------
define('SYS_PATH', realpath('../laravel').DS);

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
define('PUBLIC_PATH', realpath(__DIR__).DS);

// --------------------------------------------------------------
// Launch Laravel.
// --------------------------------------------------------------
require SYS_PATH.'laravel.php';