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
set_exception_handler(function($e) use ($application)
{
	call_user_func($application->config->get('error.handler'), $e);
});

set_error_handler(function($number, $error, $file, $line) use ($application)
{
	$exception = new \ErrorException($error, $number, 0, $file, $line);

	call_user_func($application->config->get('error.handler'), $exception);
});

register_shutdown_function(function() use ($application)
{
	if ( ! is_null($error = error_get_last()))
	{
		$exception = new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);

		call_user_func($application->config->get('error.handler'), $exception);
	}	
});

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set($application->config->get('application.timezone'));

// --------------------------------------------------------------
// Load the session and session manager.
// --------------------------------------------------------------
if ($application->config->get('session.driver') !== '')
{
	$application->session->start($application->input->cookies->get('laravel_session'), $application->config->get('session.lifetime'));
}

// --------------------------------------------------------------
// Load the packages that are in the auto-loaded packages array.
// --------------------------------------------------------------
$packages = $application->config->get('application.packages');

if (count($packages) > 0)
{
	$application->package->load($packages);
}

unset($packages);

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
$route = $application->router->route();

if ( ! is_null($route))
{
	$route->filters = require APP_PATH.'filters'.EXT;

	$response = $route->call($application);
}
else
{
	$response = $application->response->error('404');
}

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = $response->render();

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if ($application->config->get('session.driver') !== '')
{
	$application->session->close($application->input, $application->config->get('session'));
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();