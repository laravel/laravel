<?php namespace Laravel;

/**
 * Define all of the constants that we will need to use the framework.
 * These are things like file extensions, as well as all of the paths
 * used by the framework. All of the paths are built on top of the
 * basic application, laravel, and public paths.
 */
define('EXT', '.php');
define('CRLF', "\r\n");
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
 * Register the Laravel namespace so that the auto-loader loads it
 * according to the PSR-0 naming conventions. This should provide
 * fast resolution of all core classes.
 */
Autoloader::namespaces(array('Laravel' => path('sys')));

/**
 * Set the CLI options on the $_SERVER global array so we can easily
 * retrieve them from the various parts of the CLI code. We can use
 * the Request class to access them conveniently.
 */
if (defined('STDIN'))
{
	$console = CLI\Command::options($_SERVER['argv']);

	list($arguments, $options) = $console;

	$options = array_change_key_case($options, CASE_UPPER);

	$_SERVER['CLI'] = $options;
}

/**
 * The Laravel environment may be specified on the CLI using the env
 * option, allowing the developer to easily use local configuration
 * files from the CLI since the environment is usually controlled
 * by server environmenet variables.
 */
if (isset($_SERVER['CLI']['ENV']))
{
	$_SERVER['LARAVEL_ENV'] = $_SERVER['CLI']['ENV'];
}

/**
 * Finally we'll grab all of the bundles and register them with the
 * bundle class. All of the bundles are stored in an array within
 * the application directory which defines all bundles.
 */
$bundles = require path('app').'bundles'.EXT;

foreach ($bundles as $bundle => $config)
{
	Bundle::register($bundle, $config);
}