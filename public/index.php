<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.4.1
 * @author   Taylor Otwell
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// The path to the application directory.
// --------------------------------------------------------------
define('APP_PATH', realpath('../application').'/');

// --------------------------------------------------------------
// The path to the system directory.
// --------------------------------------------------------------
define('SYS_PATH', realpath($system = '../system').'/');

// --------------------------------------------------------------
// The path to the directory containing the system directory.
// --------------------------------------------------------------
define('BASE_PATH', realpath(str_replace('system', '', $system)).'/');

// --------------------------------------------------------------
// The path to the public directory.
// --------------------------------------------------------------
define('PUBLIC_PATH', realpath(__DIR__).'/');

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
$constants = array(
	'CACHE_PATH'    => APP_PATH.'storage/cache/',
	'CONFIG_PATH'   => APP_PATH.'config/',
	'DATABASE_PATH' => APP_PATH.'storage/db/',
	'LANG_PATH'     => APP_PATH.'lang/',
	'LIBRARY_PATH'  => APP_PATH.'libraries/',
	'MODEL_PATH'    => APP_PATH.'models/',
	'PACKAGE_PATH'  => APP_PATH.'packages/',
	'ROUTE_PATH'    => APP_PATH.'routes/',
	'SCRIPT_PATH'   => PUBLIC_PATH.'js/',
	'SESSION_PATH'  => APP_PATH.'storage/sessions/',
	'STORAGE_PATH'  => APP_PATH.'storage/',
	'STYLE_PATH'    => PUBLIC_PATH.'css/',
	'VIEW_PATH'     => APP_PATH.'views/',
);

foreach ($constants as $key => $value)
{
	define($key, $value);
}

unset($constants, $system);

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(function($class) 
{
	$file = strtolower(str_replace('\\', '/', $class));

	if (array_key_exists($class, $aliases = System\Config::get('aliases')))
	{
		return class_alias($aliases[$class], $class);
	}

	foreach (array(BASE_PATH, MODEL_PATH, LIBRARY_PATH) as $directory)
	{
		if (file_exists($path = $directory.$file.EXT))
		{
			require $path;

			return;
		}
	}
});

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

	System\Error::handle($e);	
});

set_error_handler(function($number, $error, $file, $line) 
{
	require_once SYS_PATH.'error'.EXT;

	System\Error::handle(new ErrorException($error, $number, 0, $file, $line));
});

register_shutdown_function(function()
{
	if ( ! is_null($error = error_get_last()))
	{
		require_once SYS_PATH.'error'.EXT;
		
		System\Error::handle(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
	}	
});

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set(System\Config::get('application.timezone'));

// --------------------------------------------------------------
// Load the session.
// --------------------------------------------------------------
if (System\Config::get('session.driver') != '')
{
	System\Session::load(System\Cookie::get('laravel_session'));
}

// --------------------------------------------------------------
// Register the route filters.
// --------------------------------------------------------------
System\Routing\Filter::register(require APP_PATH.'filters'.EXT);

// --------------------------------------------------------------
// Execute the global "before" filter.
// --------------------------------------------------------------
$response = System\Routing\Filter::call('before', array(), true);

// ----------------------------------------------------------
// Execute the route function.
// ----------------------------------------------------------
if (is_null($response))
{
	$route = System\Routing\Router::make(Request::method(), Request::uri(), new System\Routing\Loader)->route();

	$response = (is_null($route)) ? System\Response::make(System\View::make('error/404'), 404) : $route->call();
}
else
{
	$response = System\Response::prepare($response);
}

// ----------------------------------------------------------
// Execute the global "after" filter.
// ----------------------------------------------------------
System\Routing\Filter::call('after', array($response));

// ----------------------------------------------------------
// Stringify the response.
// ----------------------------------------------------------
$response->content = (string) $response->content;

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if (System\Config::get('session.driver') != '')
{
	System\Session::close();
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();