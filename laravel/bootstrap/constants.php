<?php

/**
 * Define the extensions used by the framework.
 */
define('EXT',       '.php');
define('BLADE_EXT', '.blade.php');

/**
 * Define a function that registers an array of constants
 * if they haven't already been registered. This allows the
 * constants to be changed from their default values when
 * unit testing the framework.
 */
function constants($constants)
{
	foreach ($constants as $key => $value)
	{
		if ( ! defined($key)) define($key, $value);
	}
}

/**
 * Register the core framework paths. All other paths are
 * built on top of these core paths. All of these paths are
 * changable by the developer in the front controller.
 */
$constants = array(
	'APP_PATH'     => realpath($application).'/',
	'BASE_PATH'    => realpath("$laravel/..").'/',
	'PACKAGE_PATH' => realpath($packages).'/',
	'PUBLIC_PATH'  => realpath($public).'/',
	'STORAGE_PATH' => realpath($storage).'/',
	'SYS_PATH'     => realpath($laravel).'/',
);

constants($constants);

/**
 * Register all of the other framework paths. All of these
 * paths are built on top of the core paths above.
 */
$constants = array(
	'CACHE_PATH'      => STORAGE_PATH.'cache/',
	'CONFIG_PATH'     => APP_PATH.'config/',
	'CONTROLLER_PATH' => APP_PATH.'controllers/',
	'DATABASE_PATH'   => STORAGE_PATH.'database/',
	'LANG_PATH'       => APP_PATH.'language/',
	'LIBRARY_PATH'    => APP_PATH.'libraries/',
	'MODEL_PATH'      => APP_PATH.'models/',
	'ROUTE_PATH'      => APP_PATH.'routes/',
	'SESSION_PATH'    => STORAGE_PATH.'sessions/',
	'SYS_CONFIG_PATH' => SYS_PATH.'config/',
	'SYS_LANG_PATH'   => SYS_PATH.'language/',
	'SYS_VIEW_PATH'   => SYS_PATH.'views/',
	'VIEW_PATH'       => APP_PATH.'views/',
);


constants($constants);

unset($constants);

/**
 * Set the Laravel environment configuration path constant.
 * The environment is controller by setting an environment
 * variable on the server running Laravel.
 */
$environment = (isset($_SERVER['LARAVEL_ENV'])) ? $_SERVER['LARAVEL_ENV'] : '';

define('ENV_CONFIG_PATH', $environment);

unset($environment);