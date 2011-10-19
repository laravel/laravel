<?php namespace Laravel;

require 'constants.php';

/**
 * Load the classes that can't be resolved through the auto-loader.
 * These are typically classes that are used by the auto-loader or
 * configuration classes, and therefore cannot be auto-loaded.
 */
require SYS_PATH.'arr'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'loader'.EXT;

/**
 * If a Laravel environment has been specified on the server, we will
 * add a path to the configuration manager for the environment.
 */
if (isset($_SERVER['LARAVEL_ENV']))
{
	define('ENV_CONFIG_PATH', CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/');

	Config::glance(ENV_CONFIG_PATH);
}

/**
 * Load some core configuration files by default so we don't have to
 * let them fall through the Config loader. This will allow us to
 * load these files faster for each request.
 */
foreach (array('application', 'session') as $file)
{
	$config = require CONFIG_PATH.$file.EXT;

	if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = ENV_CONFIG_PATH.$file.EXT))
	{
		$config = array_merge($config, require $path);
	}

	Config::$items[$file] = $config;
}

/**
 * Load the container configuration into the Config class. We load
 * this file manually to avoid the overhead of Config::load.
 */
Config::$items['container'] = require SYS_CONFIG_PATH.'container'.EXT;

if (file_exists($path = CONFIG_PATH.'container'.EXT))
{
	Config::$items['container'] = array_merge(Config::$items['container'], require $path);
}

if (isset($_SERVER['LARAVEL_ENV']) and file_exists($path = ENV_CONFIG_PATH.'container'.EXT))
{
	Config::$items['container'] = array_merge(Config::$items['container'], require $path);
}

/**
 * Bootstrap the application inversion of control (IoC) container.
 * The container provides the convenient resolution of objects and
 * their dependencies, allowing for flexibility and testability
 * within the framework and application.
 */
require SYS_PATH.'container'.EXT;

$container = new Container(Config::$items['container']);

IoC::$container = $container;

unset($config, $container);

/**
 * Register the application auto-loader. The auto-loader is responsible
 * for the lazy-loading of all of the Laravel core classes, as well as
 * the developer created libraries and models.
 */
spl_autoload_register(array('Laravel\\Loader', 'load'));

Loader::$aliases = Config::$items['application']['aliases'];

/**
 * Define a few convenient global functions.
 */
function e($value)
{
	return HTML::entities($value);
}

function __($key, $replacements = array(), $language = null)
{
	return Lang::line($key, $replacements, $language);
}