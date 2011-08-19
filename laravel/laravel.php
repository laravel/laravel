<?php namespace Laravel;

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Define the core framework paths.
// --------------------------------------------------------------
define('BASE_PATH',    realpath(str_replace('laravel', '', $system)).'/');
define('MODULE_PATH',  realpath($modules).'/');
define('PACKAGE_PATH', realpath($packages).'/');
define('PUBLIC_PATH',  realpath($public).'/');
define('STORAGE_PATH', realpath($storage).'/');
define('SYS_PATH',     realpath($system).'/');

unset($system, $config, $modules, $packages, $public, $storage);

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
define('CACHE_PATH',    STORAGE_PATH.'cache/');
define('CONFIG_PATH',   SYS_PATH.'config/');
define('DATABASE_PATH', STORAGE_PATH.'db/');
define('LANG_PATH',     SYS_PATH.'lang/');
define('SCRIPT_PATH',   PUBLIC_PATH.'js/');
define('SESSION_PATH',  STORAGE_PATH.'sessions/');
define('STYLE_PATH',    PUBLIC_PATH.'css/');

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'loader'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'module'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Define the default module.
// --------------------------------------------------------------
define('DEFAULT_MODULE', 'application');

// --------------------------------------------------------------
// Register the active modules.
// --------------------------------------------------------------
Module::$modules = array_merge(array('application'), $active);

unset($active);

// --------------------------------------------------------------
// Define the default module path.
// --------------------------------------------------------------
define('DEFAULT_MODULE_PATH', Module::path(DEFAULT_MODULE));

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
Loader::bootstrap(array(
	Module::path(DEFAULT_MODULE).'libraries/',
	Module::path(DEFAULT_MODULE).'models/',
));

spl_autoload_register(array('Laravel\\Loader', 'load'));

// --------------------------------------------------------------
// Set the error reporting and display levels.
// --------------------------------------------------------------
error_reporting(E_ALL | E_STRICT);

ini_set('display_errors', 'Off');

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
// Set the default timezone.
// --------------------------------------------------------------
date_default_timezone_set(Config::get('application.timezone'));

// --------------------------------------------------------------
// Load the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '') Session::load(Cookie::get('laravel_session'));

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
if (count(Config::get('application.packages')) > 0)
{
	require SYS_PATH.'package'.EXT;

	Package::load(Config::get('application.packages'));
}

// --------------------------------------------------------------
// Determine the module that should handle the request.
// --------------------------------------------------------------
$segments = explode('/', Request::uri());

define('ACTIVE_MODULE', (array_key_exists($segments[0], Module::$modules)) ? $segments[0] : DEFAULT_MODULE);

// --------------------------------------------------------------
// Determine the path to the root of the active module.
// --------------------------------------------------------------
define('ACTIVE_MODULE_PATH', Module::path(ACTIVE_MODULE));

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
	$response = Routing\Filter::call($filter, array(Request::method(), Request::uri()), true);

	if ( ! is_null($response)) break;
}

// --------------------------------------------------------------
// Route the request and get the response from the route.
// --------------------------------------------------------------
if (is_null($response))
{
	$loader = new Routing\Loader(ACTIVE_MODULE_PATH);

	$route = Routing\Router::make(Request::method(), Request::uri(), $loader)->route();

	$response = (is_null($route)) ? Response::error('404') : $route->call();
}

$response = Response::prepare($response);

// --------------------------------------------------------------
// Call the "after" filter for the application and module.
// --------------------------------------------------------------
foreach (array(ACTIVE_MODULE.'::after', 'after') as $filter)
{
	Routing\Filter::call($filter, array($response, Request::method(), Request::uri()));
}

// --------------------------------------------------------------
// Stringify the response.
// --------------------------------------------------------------
$response->content = (string) $response->content;

// --------------------------------------------------------------
// Close the session.
// --------------------------------------------------------------
if (Config::get('session.driver') != '') Session::close();

// --------------------------------------------------------------
// Send the response to the browser.
// --------------------------------------------------------------
$response->send();