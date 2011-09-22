<?php namespace Laravel;

// --------------------------------------------------------------
// Define the PHP file extensions.
// --------------------------------------------------------------
define('EXT',       '.php');
define('BLADE_EXT', '.blade.php');

// --------------------------------------------------------------
// Define the core framework paths.
// --------------------------------------------------------------
define('APP_PATH',     realpath($application).'/');
define('BASE_PATH',    realpath(str_replace('laravel', '', $laravel)).'/');
define('PACKAGE_PATH', realpath($packages).'/');
define('PUBLIC_PATH',  realpath($public).'/');
define('STORAGE_PATH', realpath($storage).'/');
define('SYS_PATH',     realpath($laravel).'/');

unset($laravel, $application, $config, $packages, $public, $storage);

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
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

// --------------------------------------------------------------
// Load the configuration manager and its dependencies.
// --------------------------------------------------------------
require SYS_PATH.'facades'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Determine the application environment.
// --------------------------------------------------------------
$environment = (isset($_SERVER['LARAVEL_ENV'])) ? $_SERVER['LARAVEL_ENV'] : null;

// --------------------------------------------------------------
// Register the configuration file paths.
// --------------------------------------------------------------
$config = array(SYS_CONFIG_PATH, CONFIG_PATH);

if ( ! is_null($environment)) $config[] = CONFIG_PATH.$environment.'/';

Config::paths($config);

// --------------------------------------------------------------
// Set a few core configuration options.
// --------------------------------------------------------------
Config::set('view.path', VIEW_PATH);

// --------------------------------------------------------------
// Bootstrap the IoC container.
// --------------------------------------------------------------
require SYS_PATH.'container'.EXT;

$container = new Container(Config::get('container'));

IoC::$container = $container;

// --------------------------------------------------------------
// Register the auto-loader on the auto-loader stack.
// --------------------------------------------------------------
spl_autoload_register(array('Laravel\\Loader', 'load'));

Loader::$paths = array(BASE_PATH, APP_PATH.'models/', APP_PATH);

Loader::$aliases = Config::get('aliases');

// --------------------------------------------------------------
// Define some convenient global functions.
// --------------------------------------------------------------
function e($value)
{
	return HTML::entities($value);
}

function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}