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
// Load the packages that are in the auto-loaded packages array.
// --------------------------------------------------------------
if (count(Config::get('application.packages')) > 0)
{
	require SYS_PATH.'package'.EXT;

	Package::load(Config::get('application.packages'));
}

// --------------------------------------------------------------
// Register the application filters.
// --------------------------------------------------------------
Routing\Filter::register(require APP_PATH.'filters'.EXT);

// --------------------------------------------------------------
// Determine the module that should handle the request.
// --------------------------------------------------------------
$segments = explode('/', Request::uri());

define('ACTIVE_MODULE', (in_array($segments[0], Config::get('application.modules'))) ? $segments[0] : 'application');

// --------------------------------------------------------------
// Determine the path to the root of the active module.
// --------------------------------------------------------------
define('ACTIVE_MODULE_PATH', (ACTIVE_MODULE == 'application') ? APP_PATH : MODULE_PATH.ACTIVE_MODULE.'/');

// --------------------------------------------------------------
// Register the filters for the active module.
// --------------------------------------------------------------
if (ACTIVE_MODULE !== 'application' and file_exists($filters = ACTIVE_MODULE_PATH.'filters'.EXT))
{
	Routing\Filter::register(require $filters);
}

// --------------------------------------------------------------
// Call the "before" filter for the application and module.
// --------------------------------------------------------------
foreach (array('before', ACTIVE_MODULE.'::before') as $filter)
{
	$response = Routing\Filter::call($filter, array(), true);
}

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
if (is_null($response))
{
	$route = Routing\Router::make(Request::method(), Request::uri(), new Routing\Loader(ACTIVE_MODULE_PATH))->route();

	$response = (is_null($route)) ? Response::error('404') : $route->call();
}

$response = Response::prepare($response);

// --------------------------------------------------------------
// Call the "after" filter for the application and module.
// --------------------------------------------------------------
foreach (array(ACTIVE_MODULE.'::after', 'after') as $filter)
{
	Routing\Filter::call($filter, array($response));
}

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