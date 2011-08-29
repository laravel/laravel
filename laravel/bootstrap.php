<?php namespace Laravel;

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

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
define('SCRIPT_PATH',     PUBLIC_PATH.'js/');
define('SESSION_PATH',    STORAGE_PATH.'sessions/');
define('STYLE_PATH',      PUBLIC_PATH.'css/');
define('SYS_CONFIG_PATH', SYS_PATH.'config/');
define('SYS_LANG_PATH',   SYS_PATH.'language/');
define('VIEW_PATH',       APP_PATH.'views/');

// --------------------------------------------------------------
// Bootstrap the application instance.
// --------------------------------------------------------------
require SYS_PATH.'application'.EXT;

$application = new Application;

// --------------------------------------------------------------
// Load the configuration manager and auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

$application->config = new Config;

$paths = array(BASE_PATH, APP_PATH.'models/', APP_PATH.'libraries/');

$application->loader = new Loader($application->config->get('aliases'), $paths);

spl_autoload_register(array($application->loader, 'load'));

unset($paths);

// --------------------------------------------------------------
// Bootstrap the IoC container.
// --------------------------------------------------------------
require SYS_PATH.'container'.EXT;

$application->container = new Container($application->config->get('container'));

// --------------------------------------------------------------
// Register the core application components in the container.
// --------------------------------------------------------------
$application->container->instance('laravel.application', $application);

$application->container->instance('laravel.config', $application->config);

$application->container->instance('laravel.loader', $application->loader);

// --------------------------------------------------------------
// Set the IoC container instance for use as a service locator.
// --------------------------------------------------------------
IoC::$container = $application->container;