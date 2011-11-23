<?php namespace Laravel;

/**
 * Define all of the constants that we will need to use the framework.
 * These are things like file extensions, as well as all of the paths
 * used by the framework. All of the paths are built on top of the
 * basic application, laravel, and public paths.
 */
define('EXT', '.php');
define('CRLF', chr(13).chr(10));
define('BLADE_EXT', '.blade.php');
define('APP_PATH', realpath($application).'/');
define('PUBLIC_PATH', realpath($public).'/');
define('SYS_PATH', realpath($laravel).'/');
define('STORAGE_PATH', APP_PATH.'storage/');
define('CACHE_PATH', STORAGE_PATH.'cache/');
define('CONFIG_PATH', APP_PATH.'config/');
define('CONTROLLER_PATH', APP_PATH.'controllers/');
define('DATABASE_PATH', STORAGE_PATH.'database/');
define('LANG_PATH', APP_PATH.'language/');
define('LIBRARY_PATH', APP_PATH.'libraries/');
define('MODEL_PATH', APP_PATH.'models/');
define('ROUTE_PATH', APP_PATH.'routes/');
define('SESSION_PATH', STORAGE_PATH.'sessions/');
define('SYS_CONFIG_PATH', SYS_PATH.'config/');
define('VIEW_PATH', APP_PATH.'views/');

/**
 * Define the Laravel environment configuration path. This path is used
 * by the configuration class to load configuration options specific for
 * the server environment, allowing the developer to conveniently change
 * configuration options based on the application environment.
 * 
 */
$environment = '';

if (isset($_SERVER['LARAVEL_ENV']))
{
	$environment = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
}

define('ENV_CONFIG_PATH', $environment);

unset($application, $public, $laravel, $environment);

/**
 * Require all of the classes that can't be loaded by the auto-loader.
 * These are typically classes that the auto-loader itself relies upon
 * to load classes, such as the array and configuration classes.
 */
require SYS_PATH.'arr'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'facades'.EXT;
require SYS_PATH.'autoloader'.EXT;

/**
 * Load a few of the core configuration files that are loaded for every
 * request to the application. It is quicker to load them manually each
 * request rather than parse the keys for every request.
 */
Config::load('application');
Config::load('session');
Config::load('error');

/**
 * Register the Autoloader's "load" method on the auto-loader stack.
 * This method provides the lazy-loading of all class files, as well
 * as any PSR-0 compliant libraries used by the application.
 */
spl_autoload_register(array('Laravel\\Autoloader', 'load'));

/**
 * Build the Laravel framework class map. This provides a super fast
 * way of resolving any Laravel class name to its appropriate path.
 * More mappings can also be registered by the developer as needed.
 */
Autoloader::$mappings = array(
	'Laravel\\Arr' => SYS_PATH.'arr'.EXT,
	'Laravel\\Asset' => SYS_PATH.'asset'.EXT,
	'Laravel\\Auth' => SYS_PATH.'auth'.EXT,
	'Laravel\\Benchmark' => SYS_PATH.'benchmark'.EXT,
	'Laravel\\Blade' => SYS_PATH.'blade'.EXT,
	'Laravel\\Config' => SYS_PATH.'config'.EXT,
	'Laravel\\Cookie' => SYS_PATH.'cookie'.EXT,
	'Laravel\\Crypter' => SYS_PATH.'crypter'.EXT,
	'Laravel\\File' => SYS_PATH.'file'.EXT,
	'Laravel\\Form' => SYS_PATH.'form'.EXT,
	'Laravel\\Hash' => SYS_PATH.'hash'.EXT,
	'Laravel\\HTML' => SYS_PATH.'html'.EXT,
	'Laravel\\Inflector' => SYS_PATH.'inflector'.EXT,
	'Laravel\\Input' => SYS_PATH.'input'.EXT,
	'Laravel\\IoC' => SYS_PATH.'ioc'.EXT,
	'Laravel\\Lang' => SYS_PATH.'lang'.EXT,
	'Laravel\\Memcached' => SYS_PATH.'memcached'.EXT,
	'Laravel\\Messages' => SYS_PATH.'messages'.EXT,
	'Laravel\\Paginator' => SYS_PATH.'paginator'.EXT,
	'Laravel\\Redirect' => SYS_PATH.'redirect'.EXT,
	'Laravel\\Redis' => SYS_PATH.'redis'.EXT,
	'Laravel\\Request' => SYS_PATH.'request'.EXT,
	'Laravel\\Response' => SYS_PATH.'response'.EXT,
	'Laravel\\Section' => SYS_PATH.'section'.EXT,
	'Laravel\\Str' => SYS_PATH.'str'.EXT,
	'Laravel\\URI' => SYS_PATH.'uri'.EXT,
	'Laravel\\URL' => SYS_PATH.'url'.EXT,
	'Laravel\\Validator' => SYS_PATH.'validator'.EXT,
	'Laravel\\View' => SYS_PATH.'view'.EXT,
	'Laravel\\Cache\\Manager' => SYS_PATH.'cache/manager'.EXT,
	'Laravel\\Cache\\Drivers\\APC' => SYS_PATH.'cache/drivers/apc'.EXT,
	'Laravel\\Cache\\Drivers\\Driver' => SYS_PATH.'cache/drivers/driver'.EXT,
	'Laravel\\Cache\\Drivers\\File' => SYS_PATH.'cache/drivers/file'.EXT,
	'Laravel\\Cache\\Drivers\\Memcached' => SYS_PATH.'cache/drivers/memcached'.EXT,
	'Laravel\\Cache\\Drivers\\Redis' => SYS_PATH.'cache/drivers/redis'.EXT,
	'Laravel\\Database\\Connection' => SYS_PATH.'database/connection'.EXT,
	'Laravel\\Database\\Expression' => SYS_PATH.'database/expression'.EXT,
	'Laravel\\Database\\Manager' => SYS_PATH.'database/manager'.EXT,
	'Laravel\\Database\\Query' => SYS_PATH.'database/query'.EXT,
	'Laravel\\Database\\Connectors\\Connector' => SYS_PATH.'database/connectors/connector'.EXT,
	'Laravel\\Database\\Connectors\\MySQL' => SYS_PATH.'database/connectors/mysql'.EXT,
	'Laravel\\Database\\Connectors\\Postgres' => SYS_PATH.'database/connectors/postgres'.EXT,
	'Laravel\\Database\\Connectors\\SQLite' => SYS_PATH.'database/connectors/sqlite'.EXT,
	'Laravel\\Database\\Eloquent\\Hydrator' => SYS_PATH.'database/eloquent/hydrator'.EXT,
	'Laravel\\Database\\Eloquent\\Model' => SYS_PATH.'database/eloquent/model'.EXT,
	'Laravel\\Database\\Grammars\\Grammar' => SYS_PATH.'database/grammars/grammar'.EXT,
	'Laravel\\Database\\Grammars\\MySQL' => SYS_PATH.'database/grammars/mysql'.EXT,
	'Laravel\\Routing\\Controller' => SYS_PATH.'routing/controller'.EXT,
	'Laravel\\Routing\\Filter' => SYS_PATH.'routing/filter'.EXT,
	'Laravel\\Routing\\Loader' => SYS_PATH.'routing/loader'.EXT,
	'Laravel\\Routing\\Route' => SYS_PATH.'routing/route'.EXT,
	'Laravel\\Routing\\Router' => SYS_PATH.'routing/router'.EXT,
	'Laravel\\Session\\Payload' => SYS_PATH.'session/payload'.EXT,
	'Laravel\\Session\\Drivers\\APC' => SYS_PATH.'session/drivers/apc'.EXT,
	'Laravel\\Session\\Drivers\\Cookie' => SYS_PATH.'session/drivers/cookie'.EXT,
	'Laravel\\Session\\Drivers\\Database' => SYS_PATH.'session/drivers/database'.EXT,
	'Laravel\\Session\\Drivers\\Driver' => SYS_PATH.'session/drivers/driver'.EXT,
	'Laravel\\Session\\Drivers\\Factory' => SYS_PATH.'session/drivers/factory'.EXT,
	'Laravel\\Session\\Drivers\\File' => SYS_PATH.'session/drivers/file'.EXT,
	'Laravel\\Session\\Drivers\\Memcached' => SYS_PATH.'session/drivers/memcached'.EXT,
	'Laravel\\Session\\Drivers\\Redis' => SYS_PATH.'session/drivers/redis'.EXT,
	'Laravel\\Session\\Drivers\\Sweeper' => SYS_PATH.'session/drivers/sweeper'.EXT,
);

/**
 * Define a few global, convenient functions. These functions
 * provide short-cuts for things like the retrieval of language
 * lines and HTML::entities. They just make our lives as devs a
 * little sweeter and more enjoyable.
 */
require SYS_PATH.'helpers'.EXT;