<?php namespace Laravel;

/**
 * Define all of the constants used by the framework. All of the core
 * paths will be defined, as well as all of the paths which derive
 * from these core paths.
 */
define('EXT', '.php');
define('CRLF', chr(13).chr(10));
define('BLADE_EXT', '.blade.php');

define('APP_PATH', realpath($application).'/');
define('BASE_PATH', realpath("$laravel/..").'/');
define('PUBLIC_PATH', realpath($public).'/');
define('SYS_PATH', realpath($laravel).'/');

define('STORAGE_PATH', APP_PATH.'storage/');
define('CACHE_PATH', STORAGE_PATH.'cache/');
define('CONFIG_PATH', APP_PATH.'config/');
define('CONTROLLER_PATH', APP_PATH.'controllers/');
define('DATABASE_PATH', STORAGE_PATH.'database/');
define('LANG_PATH', APP_PATH.'language/');
define('LIBRARY_PATH', APP_PATH.'libraries/');
define('MODEL_PATH', APP_PATH.'models/');
define('ROUTE_PATH', APP_PATH.'routes/');
define('SESSION_PATH', STORAGE_PATH.'sessions/');
define('SYS_CONFIG_PATH', SYS_PATH.'config/');
define('SYS_VIEW_PATH', SYS_PATH.'views/');
define('VIEW_PATH', APP_PATH.'views/');

/**
 * Define the Laravel environment configuration path. This path is used
 * by the configuration class to load configuration options specific
 * for the server environment.
 */
$environment = '';

if (isset($_SERVER['LARAVEL_ENV']))
{
	$environment = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
}

define('ENV_CONFIG_PATH', $environment);

unset($application, $public, $laravel, $environment);

/**
 * Require all of the classes that can't be loaded by the auto-loader.
 * These are typically classes that the auto-loader itself relies upon
 * to load classes, such as the array and configuration classes.
 */
require SYS_PATH.'arr'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'container'.EXT;
require SYS_PATH.'autoloader'.EXT;

/**
 * Load a few of the core configuration files that are loaded for every
 * request to the application. It is quicker to load them manually each
 * request rather than parse the keys for every request.
 */
Config::load('application');
Config::load('container');
Config::load('session');

/**
 * Bootstrap the application inversion of control container. The IoC
 * container is responsible for resolving classes, and helps keep the
 * framework flexible.
 */
IoC::bootstrap();

/**
 * Register the Autoloader's "load" method on the auto-loader stack.
 * This method provides the lazy-loading of all class files, as well
 * as any PSR-0 compliant libraries used by the application.
 */
spl_autoload_register(array('Laravel\\Autoloader', 'load'));

/**
 * Define a few global convenience functions to make our lives as
 * Laravel PHP developers a little more easy and enjoyable.
 */
require 'functions'.EXT;