<?php
/**
 * Laravel - A clean and classy framework for PHP web development.
 *
 * @package  Laravel
 * @version  1.1.1
 * @author   Taylor Otwell
 * @license  MIT License
 * @link     http://laravel.com 
 */

// --------------------------------------------------------------
// Define the framework paths.
// --------------------------------------------------------------
define('BASE_PATH', realpath('../').'/');
define('APP_PATH', realpath('../application').'/');
define('SYS_PATH', realpath('../system').'/');
define('PUBLIC_PATH', realpath(__DIR__.'/'));
define('PACKAGE_PATH', APP_PATH.'packages/');

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Load the configuration class.
// --------------------------------------------------------------
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(require SYS_PATH.'loader'.EXT);

// --------------------------------------------------------------
// Set the error reporting level.
// --------------------------------------------------------------
error_reporting((System\Config::get('error.detail')) ? E_ALL | E_STRICT : 0);

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
	System\Error::handle(new ErrorException($error, 0, $number, $file, $line));
});

register_shutdown_function(function()
{
	if ( ! is_null($error = error_get_last()))
	{
		require_once SYS_PATH.'error'.EXT;
		System\Error::handle(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
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
// Execute the global "before" filter.
// --------------------------------------------------------------
$response = System\Route\Filter::call('before', array(), true);

// ----------------------------------------------------------
// Execute the route function.
// ----------------------------------------------------------
if (is_null($response))
{
	$route = System\Router::route(Request::method(), Request::uri());

	if ( ! is_null($route))
	{
		$response = $route->call();	
	}
	else
	{
		$response = System\Response::make(View::make('error/404'), 404);
	}
}
else
{
	$response = System\Response::prepare($response);
}

// ----------------------------------------------------------
// Execute the global "after" filter.
// ----------------------------------------------------------
System\Route\Filter::call('after', array($response));

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
