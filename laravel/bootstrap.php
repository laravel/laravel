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
// Load the configuration manager and its dependencies.
// --------------------------------------------------------------
require SYS_PATH.'facade'.EXT;
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Bootstrap the IoC container.
// --------------------------------------------------------------
require SYS_PATH.'container'.EXT;

$dependencies = require SYS_CONFIG_PATH.'container'.EXT;

if (file_exists($path = CONFIG_PATH.'container'.EXT))
{
	$dependencies = array_merge($dependencies, require $path);
}

if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/container'.EXT))
{
	$dependencies = array_merge($dependencies, require $path);
}

$container = new Container($dependencies);

IoC::$container = $container;

// --------------------------------------------------------------
// Load the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(array($container->loader, 'load'));