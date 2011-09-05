<?php namespace Laravel;

// --------------------------------------------------------------
// Bootstrap the core framework components.
// --------------------------------------------------------------
require 'bootstrap.php';

// --------------------------------------------------------------
// Set the error reporting and display levels.
// --------------------------------------------------------------
error_reporting(-1);

ini_set('display_errors', 'Off');

// --------------------------------------------------------------
// Register the error / exception handlers.
// --------------------------------------------------------------
set_exception_handler(function($e) use ($container)
{
	call_user_func($container->config->get('error.handler'), $e);
});

set_error_handler(function($number, $error, $file, $line) use ($container)
{
	$exception = new \ErrorException($error, $number, 0, $file, $line);

	call_user_func($container->config->get('error.handler'), $exception);
});

register_shutdown_function(function() use ($container)
{
	if ( ! is_null($error = error_get_last()))
	{
		$exception = new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);

		call_user_func($container->config->get('error.handler'), $exception);
	}	
});

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set($container->config->get('application.timezone'));

// --------------------------------------------------------------
// Load the session and session manager.
// --------------------------------------------------------------
if ($container->config->get('session.driver') !== '')
{
	$cookie = $container->input->cookies->get('laravel_session');

	$container->session->start($cookie, $container->config->get('session.lifetime'));
}

// --------------------------------------------------------------
// Load the packages that are in the auto-loaded packages array.
// --------------------------------------------------------------
$packages = $container->config->get('application.packages');

if (count($packages) > 0)
{
	$container->package->load($packages);
}

unset($packages);

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
$route = $container->resolve('laravel.routing.router')->route();

if ( ! is_null($route))
{
	$route->filters = require APP_PATH.'filters'.EXT;

	$response = $container->resolve('laravel.routing.caller')->call($route);
}
else
{
	$response = $container->response->error('404');
}

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = $response->render();

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if ($container->config->get('session.driver') !== '')
{
	$container->session->close($container->input, $container->config->get('session'));
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();