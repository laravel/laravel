<?php namespace Laravel;

// --------------------------------------------------------------
// Bootstrap the core framework components.
// --------------------------------------------------------------
require 'bootstrap/core.php';

// --------------------------------------------------------------
// Register the framework error handlers.
// --------------------------------------------------------------
require SYS_PATH.'bootstrap/errors'.EXT;

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
$request = $container->resolve('laravel.request');

list($method, $uri) = array($request->method(), $request->uri());

$route = $container->resolve('laravel.routing.router')->route($request, $method, $uri);

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
	$flash = array(Input::old_input => $container->resolve('laravel.input')->get());

	$session->close($container->resolve('laravel.session'), Config::get('session'), $flash);
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();