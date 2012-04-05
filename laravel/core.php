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
| Laravel's "Artisan" CLI makes use of the Symfony Console component to
| build a wonderful CLI environment that is both robust and testable.
| We'll register the component's namespace here.
|
*/

Autoloader::namespaces(array(
	'Symfony\Component\Console' => path('base').'vendor/Symfony/Component/Console',
));

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
| Set The CLI Laravel Environment
|--------------------------------------------------------------------------
|
| Next we'll set the LARAVEL_ENV variable if the current request is from
| the Artisan command-line interface. Since the environment is often
| specified within an Apache .htaccess file, we need to set it here
| when the request is not coming through Apache.
|
*/

if (isset($_SERVER['CLI']['ENV']))
{
	$_SERVER['LARAVEL_ENV'] = $_SERVER['CLI']['ENV'];
}

/*
|--------------------------------------------------------------------------
| Register The Laravel Bundles
|--------------------------------------------------------------------------
|
| Finally we will register all of the bundles that have been defined for
| the application. None of them will be started, yet but will be setup
| so that they may be started by the develop at any time.
|
*/

$bundles = require path('app').'bundles'.EXT;

foreach ($bundles as $bundle => $config)
{
	Bundle::register($bundle, $config);
}