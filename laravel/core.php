<?php namespace Laravel;

/*
|--------------------------------------------------------------------------
| PHP Display Errors Configuration
|--------------------------------------------------------------------------
|
| Register the constants used by the framework. These are things like file
| extensions and other information that we want to be able to access with
| just a simple constant.
|
*/

define('EXT', '.php');
define('CRLF', "\r\n");
define('BLADE_EXT', '.blade.php');
define('DEFAULT_BUNDLE', 'application');
define('MB_STRING', (int) function_exists('mb_get_info'));

/*
|--------------------------------------------------------------------------
| Start Output Buffering
|--------------------------------------------------------------------------
|
| Output buffering allows us to capture all output at any time, so that we
| can discard it or treat it accordingly. An example of this is if you have
| echoed a string, but want to return a Redirect object. Because Symfony
| only checks if headers have been sent, your redirect just silently fails.
|
*/

ob_start('mb_output_handler');

/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Here we will just load in the classes that are used for every request
| or are used by the configuration class. It is quicker and simpler to
| just manually load them in instead of using the auto-loader.
|
*/

require path('sys').'ioc'.EXT;
require path('sys').'event'.EXT;
require path('sys').'bundle'.EXT;
require path('sys').'config'.EXT;
require path('sys').'helpers'.EXT;
require path('sys').'autoloader'.EXT;

/*
|--------------------------------------------------------------------------
| Register The Framework Auto-Loader
|--------------------------------------------------------------------------
|
| Next we'll register the Autoloader class on the SPL auto-loader stack
| so it can lazy-load our class files as we need them. This class and
| method will be called each time a class is needed but has not been
| defined yet and will load the appropriate file.
|
*/

spl_autoload_register(array('Laravel\\Autoloader', 'load'));

/*
|--------------------------------------------------------------------------
| Register The Laravel Namespace
|--------------------------------------------------------------------------
|
| Register the "Laravel" namespace and its directory mapping so the class
| loader can quickly load all of the core classes using PSR-0 style load
| conventions throughout the "laravel" directory since all core classes
| are namespaced into the "Laravel" namespace.
|
*/

Autoloader::namespaces(array('Laravel' => path('sys')));

/*
|--------------------------------------------------------------------------
| Register Eloquent Mappings
|--------------------------------------------------------------------------
|
| A few of the Eloquent ORM classes use a non PSR-0 naming standard so
| we will just map them with hard-coded paths here since PSR-0 uses
| underscores as directory hierarchy indicators.
|
*/

Autoloader::map(array(
	'Laravel\\Database\\Eloquent\\Relationships\\Belongs_To' 
                    => path('sys').'database/eloquent/relationships/belongs_to'.EXT,
	'Laravel\\Database\\Eloquent\\Relationships\\Has_Many' 
                    => path('sys').'database/eloquent/relationships/has_many'.EXT,
	'Laravel\\Database\\Eloquent\\Relationships\\Has_Many_And_Belongs_To' 
                    => path('sys').'database/eloquent/relationships/has_many_and_belongs_to'.EXT,
	'Laravel\\Database\\Eloquent\\Relationships\\Has_One' 
                    => path('sys').'database/eloquent/relationships/has_one'.EXT,
	'Laravel\\Database\\Eloquent\\Relationships\\Has_One_Or_Many' 
                    => path('sys').'database/eloquent/relationships/has_one_or_many'.EXT,
));

/*
|--------------------------------------------------------------------------
| Register The Symfony Components
|--------------------------------------------------------------------------
|
| Laravel makes use of the Symfony components where the situation is
| applicable and it is possible to do so. This allows us to focus
| on the parts of the framework that are unique and not re-do
| plumbing code that others have written.
|
*/

Autoloader::namespaces(array(
	'Symfony\Component\Console' 
                    => path('sys').'vendor/Symfony/Component/Console',
	'Symfony\Component\HttpFoundation'
                    => path('sys').'vendor/Symfony/Component/HttpFoundation',
));

/*
|--------------------------------------------------------------------------
| Magic Quotes Strip Slashes
|--------------------------------------------------------------------------
|
| Even though "Magic Quotes" are deprecated in PHP 5.3.x, they may still
| be enabled on the server. To account for this, we will strip slashes
| on all input arrays if magic quotes are enabled for the server.
|
*/

if (magic_quotes())
{
	$magics = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	foreach ($magics as &$magic)
	{
		$magic = array_strip_slashes($magic);
	}
}

/*
|--------------------------------------------------------------------------
| Create The HttpFoundation Request
|--------------------------------------------------------------------------
|
| Laravel uses the HttpFoundation Symfony component to handle the request
| and response functionality for the framework. This allows us to not
| worry about that boilerplate code and focus on what matters.
|
*/

use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;

Request::$foundation = RequestFoundation::createFromGlobals();

/*
|--------------------------------------------------------------------------
| Determine The Application Environment
|--------------------------------------------------------------------------
|
| Next we're ready to determine the application environment. This may be
| set either via the command line options, or, if the request is from
| the web, via the mapping of URIs to environments that lives in
| the "paths.php" file for the application and is parsed.
|
*/

if (Request::cli())
{
	$environment = get_cli_option('env');
}
else
{
	$root = Request::foundation()->getRootUrl();

	$environment = Request::detect_env($environments, $root);
}

/*
|--------------------------------------------------------------------------
| Set The Application Environment
|--------------------------------------------------------------------------
|
| Once we have determined the application environment, we will set it on
| the global server array of the HttpFoundation request. This makes it
| available throughout the application, thought it is mainly only
| used to determine which configuration files to merge in.
|
*/

if (isset($environment))
{
	Request::set_env($environment);
}

/*
|--------------------------------------------------------------------------
| Set The CLI Options Array
|--------------------------------------------------------------------------
|
| If the current request is from the Artisan command-line interface, we
| will parse the command line arguments and options and set them the
| array of options in the $_SERVER global array for convenience.
|
*/

if (defined('STDIN'))
{
	$console = CLI\Command::options($_SERVER['argv']);

	list($arguments, $options) = $console;

	$options = array_change_key_case($options, CASE_UPPER);

	$_SERVER['CLI'] = $options;
}

/*
|--------------------------------------------------------------------------
| Register The Laravel Bundles
|--------------------------------------------------------------------------
|
| Finally we will register all of the bundles that have been defined for
| the application. None of them will be started yet, but will be setup
| so that they may be started by the developer at any time.
|
*/

$bundles = require path('app').'bundles'.EXT;

foreach ($bundles as $bundle => $config)
{
	Bundle::register($bundle, $config);
}