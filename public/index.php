<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.5.0
 * @author   Taylor Otwell
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
define('APP_PATH', realpath('../application').'/');

// --------------------------------------------------------------
// The path to the system directory.
// --------------------------------------------------------------
define('SYS_PATH', realpath($system = '../system').'/');

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
define('PUBLIC_PATH', realpath(__DIR__).'/');

// --------------------------------------------------------------
// The path to the packages directory.
// --------------------------------------------------------------
define('PACKAGE_PATH', realpath('../packages').'/');

// --------------------------------------------------------------
// The path to the modules directory.
// --------------------------------------------------------------
define('MODULE_PATH', realpath('../modules').'/');

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
define('STORAGE_PATH', realpath('../storage').'/');

// --------------------------------------------------------------
// The path to the directory containing the system directory.
// --------------------------------------------------------------
define('BASE_PATH', realpath(str_replace('system', '', $system)).'/');

// --------------------------------------------------------------
// Launch Laravel.
// --------------------------------------------------------------
require SYS_PATH.'laravel.php';