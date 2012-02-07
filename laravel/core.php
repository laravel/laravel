<?php namespace Laravel;

/**
 * Define all of the constants that we will need to use the framework.
 * These are things like file extensions, as well as all of the paths
 * used by the framework. All of the paths are built on top of the
 * basic application, laravel, and public paths.
 */
define('EXT', '.php');
define('CRLF', "\r\n");
define('BLADE_EXT', '.blade.php');
define('DEFAULT_BUNDLE', 'application');
define('MB_STRING', (int) function_exists('mb_get_info'));

/**
 * Require all of the classes that can't be loaded by the auto-loader.
 * These are typically classes that the auto-loader relies upon to
 * load classes, such as the array and configuration classes.
 */
require path('sys').'event'.EXT;
require path('sys').'bundle'.EXT;
require path('sys').'config'.EXT;
require path('sys').'helpers'.EXT;
require path('sys').'autoloader'.EXT;

/**
 * Register the Autoloader's "load" method on the auto-loader stack.
 * This method provides the lazy-loading of all class files, as well
 * as any PSR-0 compliant libraries used by the application.
 */
spl_autoload_register(array('Laravel\\Autoloader', 'load'));

/**
 * Register all of the core class aliases. These aliases provide a
 * convenient way of working with the Laravel core classes without
 * having to worry about the namespacing. The developer is also
 * free to remove aliases when they extend core classes.
 */
Autoloader::$aliases = Config::get('application.aliases');

/**
 * Register the Laravel namespace so that the auto-loader loads it
 * according to the PSR-0 naming conventions. This should provide
 * fast resolution of all core classes.
 */
Autoloader::namespaces(array('Laravel' => path('sys')));

/**
 * Grab the bundle manifest for the application. This contains an
 * array of all of the installed bundles, plus information about
 * each of them. If it's not cached, we'll detect them and then
 * cache it to save time later.
 */
$bundles = Cache::remember('laravel.bundle.manifest', function()
{
	return Bundle::detect(path('bundle'));

}, Config::get('application.bundle.cache'));

/**
 * Register all of the bundles that are defined in the main bundle
 * manifest. This informs the framework where the bundle lives
 * and which URIs it can respnod to.
 */
foreach ($bundles as $bundle)
{
	Bundle::register($bundle);
}

/**
 * Register the default timezone for the application. This will
 * be the default timezone used by all date functions through
 * throughout the entire application.
 */
$timezone = Config::get('application.timezone');

date_default_timezone_set($timezone);