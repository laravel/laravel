<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.5.0
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
// The path to the public directory.
// --------------------------------------------------------------
define('PUBLIC_PATH', realpath(__DIR__).'/');

// --------------------------------------------------------------
// The path to the packages directory.
// --------------------------------------------------------------
define('PACKAGE_PATH', realpath('../packages').'/');

// --------------------------------------------------------------
// The path to the modules directory.
// --------------------------------------------------------------
define('MODULE_PATH', realpath('../modules').'/');

// --------------------------------------------------------------
// The path to the storage directory.
// --------------------------------------------------------------
define('STORAGE_PATH', realpath('../storage').'/');

// --------------------------------------------------------------
// The path to the directory containing the system directory.
// --------------------------------------------------------------
define('BASE_PATH', realpath(str_replace('system', '', $system)).'/');

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
$constants = array(
	'CACHE_PATH'    => STORAGE_PATH.'cache/',
	'CONFIG_PATH'   => APP_PATH.'config/',
	'DATABASE_PATH' => STORAGE_PATH.'db/',
	'LANG_PATH'     => APP_PATH.'lang/',
	'LIBRARY_PATH'  => APP_PATH.'libraries/',
	'MODEL_PATH'    => APP_PATH.'models/',
	'ROUTE_PATH'    => APP_PATH.'routes/',
	'SCRIPT_PATH'   => PUBLIC_PATH.'js/',
	'SESSION_PATH'  => STORAGE_PATH.'sessions/',
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
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;
// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
System\Loader::bootstrap();

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
require SYS_PATH.'package'.EXT;

System\Package::load(System\Config::get('application.packages'));

// --------------------------------------------------------------
// Register the route filters.
// --------------------------------------------------------------
System\Routing\Filter::register(require APP_PATH.'filters'.EXT);

// --------------------------------------------------------------
// Execute the global "before" filter.
// --------------------------------------------------------------
$response = System\Routing\Filter::call('before', array(), true);

// --------------------------------------------------------------
// Execute the route function.
// --------------------------------------------------------------
if (is_null($response))
{
	$segments = explode('/', $uri = System\Request::uri());

	if (in_array($segments[0], System\Config::get('application.modules')))
	{
		$route_path = MODULE_PATH.$segments[0].'/';

		if (file_exists($filters = $route_path.'filters'.EXT))
		{
			System\Routing\Filter::register(require $filters);
		}
	}
	else
	{
		$route_path = APP_PATH;
	}

	$route = System\Routing\Router::make(System\Request::method(), $uri, new System\Routing\Loader($route_path))->route();

	$response = (is_null($route)) ? System\Response::error('404') : $route->call();
}
else
{
	$response = System\Response::prepare($response);
}

// --------------------------------------------------------------
// Execute the global "after" filter.
// --------------------------------------------------------------
System\Routing\Filter::call('after', array($response));

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
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