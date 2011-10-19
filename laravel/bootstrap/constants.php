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

$constants = array(
	'APP_PATH'     => realpath($application).'/',
	'BASE_PATH'    => realpath("$laravel/..").'/',
	'PACKAGE_PATH' => realpath($packages).'/',
	'PUBLIC_PATH'  => realpath($public).'/',
	'STORAGE_PATH' => realpath($storage).'/',
	'SYS_PATH'     => realpath($laravel).'/',
);

constants($constants);

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