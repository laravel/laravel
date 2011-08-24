<?php namespace Laravel;

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Define the core framework paths.
// --------------------------------------------------------------
define('APP_PATH',     realpath($application).'/');
define('BASE_PATH',    realpath(str_replace('laravel', '', $laravel)).'/');
define('MODULE_PATH',  realpath($modules).'/');
define('PACKAGE_PATH', realpath($packages).'/');
define('PUBLIC_PATH',  realpath($public).'/');
define('STORAGE_PATH', realpath($storage).'/');
define('SYS_PATH',     realpath($laravel).'/');

unset($laravel, $application, $config, $modules, $packages, $public, $storage);

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
define('CACHE_PATH',    STORAGE_PATH.'cache/');
define('CONFIG_PATH',   APP_PATH.'config/');
define('DATABASE_PATH', STORAGE_PATH.'db/');
define('LANG_PATH',     SYS_PATH.'lang/');
define('SCRIPT_PATH',   PUBLIC_PATH.'js/');
define('SESSION_PATH',  STORAGE_PATH.'sessions/');
define('STYLE_PATH',    PUBLIC_PATH.'css/');

// --------------------------------------------------------------
// Define the default module and path.
// --------------------------------------------------------------
define('DEFAULT_MODULE', 'application');

define('DEFAULT_MODULE_PATH', APP_PATH);

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'module'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Register the active modules.
// --------------------------------------------------------------
Module::$modules = array_merge(array(DEFAULT_MODULE => DEFAULT_MODULE_PATH), $active);

unset($active);

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
Loader::bootstrap(array(
	APP_PATH.'libraries/',
	APP_PATH.'models/',
));

spl_autoload_register(array('Laravel\\Loader', 'load'));

// --------------------------------------------------------------
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set(Config::get('application.timezone'));

// --------------------------------------------------------------
// Set the error reporting and display levels.
// --------------------------------------------------------------
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 'Off');

// --------------------------------------------------------------
// Initialize the request instance for the request.
// --------------------------------------------------------------
$request = new Request($_SERVER);

// --------------------------------------------------------------
// Hydrate the input for the current request.
// --------------------------------------------------------------
$request->input = new Input($request, $_GET, $_POST, $_COOKIE, $_FILES);

// --------------------------------------------------------------
// Determine the module that should handle the request.
// --------------------------------------------------------------
$segments = explode('/', $request->uri());

define('ACTIVE_MODULE', (array_key_exists($segments[0], Module::$modules)) ? $segments[0] : DEFAULT_MODULE);

// --------------------------------------------------------------
// Determine the path to the root of the active module.
// --------------------------------------------------------------
define('ACTIVE_MODULE_PATH', Module::path(ACTIVE_MODULE));

// --------------------------------------------------------------
// Register the error / exception handlers.
// --------------------------------------------------------------
$error_dependencies = function()
{
	require_once SYS_PATH.'exception/handler'.EXT;
	require_once SYS_PATH.'exception/examiner'.EXT;
	require_once SYS_PATH.'file'.EXT;
};

set_exception_handler(function($e) use ($error_dependencies)
{
	call_user_func($error_dependencies);

	Exception\Handler::make($e)->handle();
});

set_error_handler(function($number, $error, $file, $line) use ($error_dependencies)
{
	call_user_func($error_dependencies);

	Exception\Handler::make(new \ErrorException($error, $number, 0, $file, $line))->handle();
});

register_shutdown_function(function() use ($error_dependencies)
{
	if ( ! is_null($error = error_get_last()))
	{
		call_user_func($error_dependencies);

		extract($error);

		Exception\Handler::make(new \ErrorException($message, $type, 0, $file, $line))->handle();
	}	
});

// --------------------------------------------------------------
// Load the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '') Session::driver()->start(Cookie::get('laravel_session'));

// --------------------------------------------------------------
// Load all of the core routing classes.
// --------------------------------------------------------------
require SYS_PATH.'response'.EXT;
require SYS_PATH.'routing/route'.EXT;
require SYS_PATH.'routing/router'.EXT;
require SYS_PATH.'routing/loader'.EXT;
require SYS_PATH.'routing/filter'.EXT;

// --------------------------------------------------------------
// Load the packages that are in the auto-loaded packages array.
// --------------------------------------------------------------
if (count(Config::get('application.packages')) > 0)
{
	require SYS_PATH.'package'.EXT;

	Package::load(Config::get('application.packages'));
}

// --------------------------------------------------------------
// Register the filters for the default module.
// --------------------------------------------------------------
Routing\Filter::register(require DEFAULT_MODULE_PATH.'filters'.EXT);

// --------------------------------------------------------------
// Register the filters for the active module.
// --------------------------------------------------------------
if (file_exists(ACTIVE_MODULE_PATH.'filters'.EXT))
{
	Routing\Filter::register(require ACTIVE_MODULE_PATH.'filters'.EXT);	
}

// --------------------------------------------------------------
// Call the "before" filter for the application and module.
// --------------------------------------------------------------
foreach (array('before', ACTIVE_MODULE.'::before') as $filter)
{
	$response = Routing\Filter::call($filter, array($request->method(), $request->uri()), true);

	if ( ! is_null($response)) break;
}

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
if (is_null($response))
{
	$loader = new Routing\Loader(ACTIVE_MODULE_PATH);

	$route = Routing\Router::make($request, $loader)->route();

	$response = (is_null($route)) ? Response::error('404') : $route->call();
}

$response = Response::prepare($response);

// --------------------------------------------------------------
// Call the "after" filter for the application and module.
// --------------------------------------------------------------
foreach (array(ACTIVE_MODULE.'::after', 'after') as $filter)
{
	Routing\Filter::call($filter, array($response, $request->method(), $request->uri()));
}

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = ($response->content instanceof View) ? $response->content->get() : (string) $response->content;

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '')
{
	$driver = Session::driver();

	$driver->flash('laravel_old_input', $request->input->get());

	$driver->close();

	if ($driver instanceof Session\Sweeper and mt_rand(1, 100) <= 2)
	{
		$driver->sweep(time() - (Config::get('session.lifetime') * 60));
	}
}

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();