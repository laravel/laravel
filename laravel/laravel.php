<?php namespace Laravel;

/**
 * Bootstrap the core framework components like the IoC container and
 * the configuration class, and the class auto-loader. Once this file
 * has run, the framework is essentially ready for use.
 */
require 'core.php';

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

ini_set('display_errors', Config::get('error.display'));

/**
 * Determine if we need to set the application key to a very random
 * string so we can provide a zero configuration installation but
 * still ensure that the key is set to something random. It is
 * possible to disable this feature.
 */
$auto_key = Config::get('application.auto_key');

if ($auto_key and Config::get('application.key') == '')
{
	ob_start() and with(new CLI\Tasks\Key)->generate();

	ob_end_clean();
}

/**
 * Even though "Magic Quotes" are deprecated in PHP 5.3, they may
 * still be enabled on the server. To account for this, we will
 * strip slashes on all input arrays if magic quotes are turned
 * on for the server environment.
 */
if (magic_quotes())
{
	$magics = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	foreach ($magics as &$magic)
	{
		$magic = array_strip_slashes($magic);
	}
}

/**
 * Load the session using the session manager. The payload will
 * be set on a static property of the Session class for easy
 * access throughout the framework and application.
 */
if (Config::get('session.driver') !== '')
{
	Session::start(Config::get('session.driver'));

	Session::load(Cookie::get(Config::get('session.cookie')));
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

			if (magic_quotes()) $input = array_strip_slashes($input);
		}
}

/**
 * The spoofed request method is removed from the input so it is not
 * unexpectedly included in Input::all() or Input::get(). Leaving it
 * in the input array could cause unexpected results if an Eloquent
 * model is filled with the input.
 */
unset($input[Request::spoofer]);

Input::$input = $input;

/**
 * Load the "application" bundle. Though the application folder is
 * not typically considered a bundle, it is started like one and
 * essentially serves as the "default" bundle.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * Auto-start any bundles configured to start on every request.
 * This is especially useful for debug bundles or bundles that
 * are used throughout the application.
 */
foreach (Bundle::$bundles as $bundle => $config)
{
	if ($config['auto']) Bundle::start($bundle);
}

/**
 * Register the "catch-all" route that handles 404 responses for
 * routes that can not be matched to any other route within the
 * application. We'll just raise the 404 event.
 */
Routing\Router::register('*', '(:all)', function()
{
	return Event::first('404');
});

/**
 * If the requset URI has too many segments, we will bomb out of
 * the request. This is too avoid potential DDoS attacks against
 * the framework by overloading the controller lookup method
 * with thousands of segments.
 */
$uri = URI::current();

if (count(URI::$segments) > 15)
{
	throw new \Exception("Invalid request. Too many URI segments.");
}

/**
 * Route the request to the proper route in the application. If a
 * route is found, the route will be called via the request class
 * static property. If no route is found, the 404 response will
 * be returned to the browser.
 */
Request::$route = Routing\Router::route(Request::method(), $uri);

$response = Request::$route->call();

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

/**
 * Send all of the cookies to the browser. The cookies are
 * stored in a "jar" until the end of a request, primarily
 * to make testing the cookie functionality of the site
 * much easier since the jar can be inspected.
 */
Cookie::send();	

/**
 * Send the final response to the browser and fire the
 * final event indicating that the processing for the
 * current request is completed.
 */
$response->send();

Event::fire('laravel.done');