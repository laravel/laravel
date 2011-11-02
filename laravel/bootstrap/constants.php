<?php

define('EXT',       '.php');
define('BLADE_EXT', '.blade.php');

function constants($constants)
{
	foreach ($constants as $key => $value)
	{
		if ( ! defined($key)) define($key, $value);
	}
}

/**
 * Register the core framework paths and all of the paths that
 * derive from them. If any constant has already been defined,
 * we will not attempt to define it again.
 */
$constants = array(
	'APP_PATH'     => realpath($application).'/',
	'BASE_PATH'    => realpath("$laravel/..").'/',
	'PUBLIC_PATH'  => realpath($public).'/',
	'STORAGE_PATH' => realpath($storage).'/',
	'SYS_PATH'     => realpath($laravel).'/',
);

constants($constants);

unset($application, $public, $storage, $laravel);

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
	'SYS_VIEW_PATH'   => SYS_PATH.'views/',
	'VIEW_PATH'       => APP_PATH.'views/',
);


constants($constants);

/**
 * Register the core framework paths and all of the paths that
 * derive from them. If any constant has already been defined,
 * we will not attempt to define it again.
 */
$environment = (isset($_SERVER['LARAVEL_ENV'])) ? CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/' : '';

constants(array('ENV_CONFIG_PATH' => $environment));

unset($constants, $environment);