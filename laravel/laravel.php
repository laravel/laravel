<?php namespace Laravel;

/**
 * Bootstrap the core framework components like the IoC container and
 * the configuration class, and the class auto-loader. Once this file
 * has run, the framework is essentially ready for use.
 */
require 'core.php';

/**
 * Register the default timezone for the application. This will be the
 * default timezone used by all date / timezone functions throughout
 * the entire application.
 */
date_default_timezone_set(Config::get('application.timezone'));
/**
 * Register the PHP exception handler. The framework throws exceptions
 * on every error that cannot be handled. All of those exceptions will
 * be sent through this closure for processing.
 */
set_exception_handler(function($e)
{
	Error::exception($e);
});

/**
 * Register the PHP error handler. All PHP errors will fall into this
 * handler which will convert the error into an ErrorException object
 * and pass the exception into the exception handler.
 */
set_error_handler(function($code, $error, $file, $line)
{
	Error::native($code, $error, $file, $line);
});

/**
 * Register the shutdown handler. This function will be called at the
 * end of the PHP script or on a fatal PHP error. If a PHP error has
 * occured, we will convert it to an ErrorException and pass it
 * to the common exception handler for the framework.
 */
register_shutdown_function(function()
{
	Error::shutdown();
});

/**
 * Setting the PHP error reporting level to -1 essentially forces
 * PHP to report every error, and it is guranteed to show every
 * error on future versions of PHP.
 *
 * If error detail is turned off, we will turn off all PHP error
 * reporting and display since the framework will be displaying
 * a generic message and we do not want any sensitive details
 * about the exception leaking into the views.
 */
error_reporting(-1);

ini_set('display_errors', 'Off');

/**
 * Load the session using the session manager. The payload will
 * be registered in the IoC container as an instance so it can
 * be easily access throughout the framework.
 */
if (Config::get('session.driver') !== '')
{
	Session::start(Config::get('session.driver'));

	Session::load(Cookie::get(Config::get('session.cookie')));

	IoC::instance('laravel.session', Session::$instance);
}

/**
 * Gather the input to the application based on the global input
 * variables for the current request. The input will be gathered
 * based on the current request method and will be set on the
 * Input manager class' static $input property.
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

/**
 * The spoofed request method is removed from the input so it is not
 * unexpectedly included in Input::all() or Input::get(). Leaving it
 * in the input array could cause unexpected results if an Eloquent
 * model is filled with the input.
 */
unset($input[Request::spoofer]);

if (function_exists('get_magic_quotes_gpc') and get_magic_quotes_gpc())
{
	$input = stripslashes($input);
}

Input::$input = $input;

/**
 * Start all of the bundles that are specified in the configuration
 * array of auto-loaded bundles. This lets the developer have an
 * easy way to load bundles for every request.
 */
foreach (Config::get('application.bundles') as $bundle)
{
	Bundle::start($bundle);
}

/**
 * Load the "application" bundle. Though the application folder is
 * not typically considered a bundle, it is started like one and
 * essentially serves as the "default" bundle.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * If the first segment of the URI corresponds with a bundle we'll
 * start that bundle. By convention, bundles handle all URIs which
 * begin with their bundle name.
 */
$bundle = URI::segment(1);

if ( ! is_null($bundle) and Bundle::routable($bundle))
{
	Bundle::start($bundle);
}

/**
 * Route the request to the proper route in the application. If a
 * route is found, the route will be called via the request class
 * static property. If no route is found, the 404 response will
 * be returned to the browser.
 */
if (count(URI::$segments) > 15)
{
	throw new \Exception("Invalid request. Too many URI segments.");
}

Request::$route = Routing\Router::route(Request::method(), URI::current());

if ( ! is_null(Request::$route))
{
	$response = Request::$route->call();
}
else
{
	$response = Response::error('404');
}

/**
 * Close the session and write the active payload to persistent
 * storage. The session cookie will also be written and if the
 * driver is a sweeper, session garbage collection might be
 * performed depending on the "sweepage" probability.
 */
if (Config::get('session.driver') !== '')
{
	Session::save();
}

$response->send();