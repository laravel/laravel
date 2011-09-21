<?php namespace Laravel;

// --------------------------------------------------------------
// Bootstrap the core framework components.
// --------------------------------------------------------------
require 'core.php';

// --------------------------------------------------------------
// Get an instance of the configuration manager.
// --------------------------------------------------------------
set_exception_handler(function($e)
{
	call_user_func(Config::get('error.handler'), $e);
});

set_error_handler(function($number, $error, $file, $line)
{
	$exception = new \ErrorException($error, $number, 0, $file, $line);

	call_user_func(Config::get('error.handler'), $exception);
});

register_shutdown_function(function()
{
	if ( ! is_null($error = error_get_last()))
	{
		$exception = new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);

		call_user_func(Config::get('error.handler'), $exception);
	}	
});

// --------------------------------------------------------------
// Set the error reporting and display levels.
// --------------------------------------------------------------
error_reporting(-1);

ini_set('display_errors', 'Off');

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set(Config::get('application.timezone'));

// --------------------------------------------------------------
// Load the session and session manager.
// --------------------------------------------------------------
if (Config::get('session.driver') !== '')
{
	$session = $container->resolve('laravel.session.manager');

	$container->instance('laravel.session', $session->payload(Config::get('session')));
}

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
$route = $container->resolve('laravel.routing.router')->route(Request::method(), Request::uri());

if ( ! is_null($route))
{
	$response = $container->resolve('laravel.routing.caller')->call($route);
}
else
{
	$response = Response::error('404');
}

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = $response->render();

// --------------------------------------------------------------
// Close the session and write the session cookie.
// --------------------------------------------------------------
if (isset($session))
{
	$session->close($container->resolve('laravel.session'), Config::get('session'));
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();