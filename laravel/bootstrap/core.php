<?php namespace Laravel;

/**
 * Define core framework paths and constants.
 */
define('APP_PATH',     realpath($application).'/');
define('BASE_PATH',    realpath(str_replace('laravel', '', $laravel)).'/');
define('PACKAGE_PATH', realpath($packages).'/');
define('PUBLIC_PATH',  realpath($public).'/');
define('STORAGE_PATH', realpath($storage).'/');
define('SYS_PATH',     realpath($laravel).'/');

unset($laravel, $application, $config, $packages, $public, $storage);

define('CACHE_PATH',      STORAGE_PATH.'cache/');
define('CONFIG_PATH',     APP_PATH.'config/');
define('CONTROLLER_PATH', APP_PATH.'controllers/');
define('DATABASE_PATH',   STORAGE_PATH.'database/');
define('LANG_PATH',       APP_PATH.'language/');
define('ROUTE_PATH',      APP_PATH.'routes/');
define('SESSION_PATH',    STORAGE_PATH.'sessions/');
define('SYS_CONFIG_PATH', SYS_PATH.'config/');
define('SYS_LANG_PATH',   SYS_PATH.'language/');
define('VIEW_PATH',       APP_PATH.'views/');

define('EXT',       '.php');
define('BLADE_EXT', '.blade.php');

/**
 * Load the classes that can't be resolved through the auto-loader. These are typically classes
 * that are used by the auto-loader or configuration classes, and therefore cannot be auto-loaded.
 */
require SYS_PATH.'facades'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'arr'.EXT;

/**
 * Determine the application environment. The environment is typically set by an environment
 * variable on the server, as this provides the most accident-proof method of handling
 * application environments. However, the environment could be manually set by the developer
 * in the front controller if access to the environment variables is not available.
 * set by an environment variable on the server.
 */
$environment = (isset($_SERVER['LARAVEL_ENV'])) ? $_SERVER['LARAVEL_ENV'] : null;

/**
 * Register the path to the configuration files.
 */
$configs = array(SYS_CONFIG_PATH, CONFIG_PATH);

if ( ! is_null($environment)) $configs[] = CONFIG_PATH.$environment.'/';

Config::$paths = $configs;

/**
 * Bootstrap the application inversion of control (IoC) container. The container provides the
 * convenient resolution of objects and their dependencies, allowing for flexibility and
 * testability within the framework and application.
 */
require SYS_PATH.'container'.EXT;

$container = new Container(Config::get('container'));

IoC::$container = $container;

/**
 * Register the application auto-loader. The auto-loader is responsible for the lazy-loading
 * of all of the Laravel core classes, as well as the developer created libraries and models.
 */
spl_autoload_register(array('Laravel\\Loader', 'load'));

Loader::$paths = array(BASE_PATH, APP_PATH.'models/', APP_PATH.'libraries/', APP_PATH);

Loader::$aliases = Config::get('aliases');

/**
 * Define a few convenient global functions.
 */
function e($value)
{
	return HTML::entities($value);
}

function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}