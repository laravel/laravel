<?php namespace System;

// --------------------------------------------------------------
// Define the core framework paths.
// --------------------------------------------------------------
define('APP_PATH',     realpath($application).'/');
define('SYS_PATH',     realpath($system).'/');
define('PUBLIC_PATH',  realpath($public).'/');
define('PACKAGE_PATH', realpath($packages).'/');
define('MODULE_PATH',  realpath($modules).'/');
define('STORAGE_PATH', realpath($storage).'/');
define('BASE_PATH',    realpath(str_replace('system', '', $system)).'/');

unset($application, $system, $public, $packages, $modules, $storage);

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
define('CACHE_PATH',    STORAGE_PATH.'cache/');
define('CONFIG_PATH',   APP_PATH.'config/');
define('DATABASE_PATH', STORAGE_PATH.'db/');
define('LANG_PATH',     APP_PATH.'lang/');
define('LIBRARY_PATH',  APP_PATH.'libraries/');
define('MODEL_PATH',    APP_PATH.'models/');
define('ROUTE_PATH',    APP_PATH.'routes/');
define('SCRIPT_PATH',   PUBLIC_PATH.'js/');
define('SESSION_PATH',  STORAGE_PATH.'sessions/');
define('STYLE_PATH',    PUBLIC_PATH.'css/');
define('VIEW_PATH',     APP_PATH.'views/');

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Detect all of the application modules.
// --------------------------------------------------------------
Config::set('application.modules', $modules = array_map('basename', glob(MODULE_PATH.'*', GLOB_ONLYDIR)));

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
Loader::bootstrap();

spl_autoload_register(array('System\\Loader', 'load'));

// --------------------------------------------------------------
// Set the error reporting and display levels.
// --------------------------------------------------------------
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 'Off');

// --------------------------------------------------------------
// Register the error handlers.
// --------------------------------------------------------------
set_exception_handler(function($e)
{
	require_once SYS_PATH.'error'.EXT;

	Error::handle($e);	
});

set_error_handler(function($number, $error, $file, $line) 
{
	require_once SYS_PATH.'error'.EXT;

	Error::handle(new \ErrorException($error, $number, 0, $file, $line));
});

register_shutdown_function(function()
{
	if ( ! is_null($error = error_get_last()))
	{
		require_once SYS_PATH.'error'.EXT;
		
		Error::handle(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
	}	
});

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set(Config::get('application.timezone'));

// --------------------------------------------------------------
// Load the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '')
{
	Session::load(Cookie::get('laravel_session'));
}

// --------------------------------------------------------------
// Load all of the core routing classes.
// --------------------------------------------------------------
require SYS_PATH.'request'.EXT;
require SYS_PATH.'response'.EXT;
require SYS_PATH.'routing/route'.EXT;
require SYS_PATH.'routing/router'.EXT;
require SYS_PATH.'routing/loader'.EXT;
require SYS_PATH.'routing/filter'.EXT;

// --------------------------------------------------------------
// Register the route filters.
// --------------------------------------------------------------
Routing\Filter::register(require APP_PATH.'filters'.EXT);

// --------------------------------------------------------------
// Load the packages that are in the auto-loaded packages array.
// --------------------------------------------------------------
require SYS_PATH.'package'.EXT;

Package::load(Config::get('application.packages'));

// --------------------------------------------------------------
// Execute the global "before" filter.
// --------------------------------------------------------------
$response = Routing\Filter::call('before', array(), true);

// --------------------------------------------------------------
// Route the request and call the appropriate route function.
// --------------------------------------------------------------
if (is_null($response))
{
	if (in_array($module = Request::segment(1), $modules))
	{
		define('ACTIVE_MODULE', $module);

		$path = MODULE_PATH.$module.'/';

		if (file_exists($filters = $path.'filters'.EXT))
		{
			Routing\Filter::register(require $filters);
		}
	}
	else
	{
		define('ACTIVE_MODULE', 'application');

		$path = APP_PATH;
	}

	$route = Routing\Router::make(Request::method(), Request::uri(), new Routing\Loader($path))->route();

	$response = (is_null($route)) ? Response::error('404') : $route->call();
}
else
{
	$response = Response::prepare($response);
}

// --------------------------------------------------------------
// Execute the global "after" filter.
// --------------------------------------------------------------
Routing\Filter::call('after', array($response));

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = (string) $response->content;

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '')
{
	Session::close();
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();