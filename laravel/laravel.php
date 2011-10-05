<?php namespace Laravel;

/**
 * Bootstrap the core framework components like the IoC container,
 * configuration class, and the class auto-loader. Once this file
 * has run, the framework is essentially ready for use.
 */
require 'bootstrap/core.php';

/**
 * Register the framework error handling methods and set the
 * error_reporting levels. This file will register handlers
 * for exceptions, errors, and shutdown.
 */
require SYS_PATH.'bootstrap/errors'.EXT;

/**
 * Set the application's default timezone.
 */
date_default_timezone_set(Config::get('application.timezone'));

/**
 * Load the session and session manager instance. The session
 * payload will be registered in the IoC container as an instance
 * so it can be retrieved easily throughout the application.
 */
if (Config::get('session.driver') !== '')
{
	$session = $container->core('session.manager');

	$container->instance('laravel.session', $session->payload(Config::get('session')));
}

/**
 * Manually load some core classes that are used on every request
 * This allows to avoid using the loader for these classes.
 */
require SYS_PATH.'uri'.EXT;
require SYS_PATH.'request'.EXT;
require SYS_PATH.'routing/route'.EXT;
require SYS_PATH.'routing/router'.EXT;
require SYS_PATH.'routing/loader'.EXT;
require SYS_PATH.'routing/caller'.EXT;

/**
 * Gather the input to the application for the current request.
 * The input will be gathered based on the current request method
 * and will be set on the Input manager.
 */
$input = array();

switch (Request::method())
{
	case 'GET':
		$input = $_GET;
		break;

	case 'POST':
		$input = $_POST;
		break;

	case 'PUT':
	case 'DELETE':
		if (Request::spoofed())
		{
			$input = $_POST;
		}
		else
		{
			parse_str(file_get_contents('php://input'), $input);
		}
}

unset($input[Request::spoofer]);

Input::set($input);

/**
 * Route the request to the proper route in the application. If a
 * route is found, the route will be called with the current request
 * instance. If no route is found, the 404 response will be returned
 * to the browser.
 */
list($method, $uri) = array(Request::method(), URI::get());

$route = $container->core('routing.router')->route($method, $uri);

if ( ! is_null($route))
{
	$response = $container->core('routing.caller')->call($route);
}
else
{
	$response = Response::error('404');
}

/**
 * Stringify the response. We need to force the response to be
 * stringed before closing the session, since the developer may
 * be using the session within their views, so we cannot age
 * the session data until the view is rendered.
 */
$response->content = $response->render();

/**
 * Close the session and write the active payload to persistent
 * storage. The input for the current request is also flashed
 * to the session so it will be available for the next request
 * via the Input::old method.
 */
if (isset($session))
{
	$flash = array(Input::old_input => Input::get());

	$session->close($container->core('session'), Config::get('session'), $flash);
}

/**
 * Finally, we can send the response to the browser.
 */
$response->send();