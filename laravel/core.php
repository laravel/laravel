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
define('CACHE_PATH', STORAGE_PATH.'cache'.DS);
define('DATABASE_PATH', STORAGE_PATH.'database'.DS);
define('SESSION_PATH', STORAGE_PATH.'sessions'.DS);
define('DEFAULT_BUNDLE', 'application');
define('MB_STRING', (int) function_exists('mb_get_info'));

/**
 * Require all of the classes that can't be loaded by the auto-loader.
 * These are typically classes that the auto-loader itself relies upon
 * to load classes, such as the array and configuration classes.
 */
require SYS_PATH.'bundle'.EXT;
require SYS_PATH.'config'.EXT;
require SYS_PATH.'helpers'.EXT;
require SYS_PATH.'autoloader'.EXT;

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
	'Laravel\\Auth' => SYS_PATH.'auth'.EXT,
	'Laravel\\Asset' => SYS_PATH.'asset'.EXT,
	'Laravel\\Benchmark' => SYS_PATH.'benchmark'.EXT,
	'Laravel\\Blade' => SYS_PATH.'blade'.EXT,
	'Laravel\\Bundle' => SYS_PATH.'bundle'.EXT,
	'Laravel\\Cache' => SYS_PATH.'cache'.EXT,
	'Laravel\\Config' => SYS_PATH.'config'.EXT,
	'Laravel\\Cookie' => SYS_PATH.'cookie'.EXT,
	'Laravel\\Crypter' => SYS_PATH.'crypter'.EXT,
	'Laravel\\Database' => SYS_PATH.'database'.EXT,
	'Laravel\\Error' => SYS_PATH.'error'.EXT,
	'Laravel\\Event' => SYS_PATH.'event'.EXT,
	'Laravel\\File' => SYS_PATH.'file'.EXT,
	'Laravel\\Fluent' => SYS_PATH.'fluent'.EXT,
	'Laravel\\Form' => SYS_PATH.'form'.EXT,
	'Laravel\\Hash' => SYS_PATH.'hash'.EXT,
	'Laravel\\HTML' => SYS_PATH.'html'.EXT,
	'Laravel\\Input' => SYS_PATH.'input'.EXT,
	'Laravel\\IoC' => SYS_PATH.'ioc'.EXT,
	'Laravel\\Lang' => SYS_PATH.'lang'.EXT,
	'Laravel\\Log' => SYS_PATH.'log'.EXT,
	'Laravel\\Memcached' => SYS_PATH.'memcached'.EXT,
	'Laravel\\Messages' => SYS_PATH.'messages'.EXT,
	'Laravel\\Paginator' => SYS_PATH.'paginator'.EXT,
	'Laravel\\Redirect' => SYS_PATH.'redirect'.EXT,
	'Laravel\\Redis' => SYS_PATH.'redis'.EXT,
	'Laravel\\Request' => SYS_PATH.'request'.EXT,
	'Laravel\\Response' => SYS_PATH.'response'.EXT,
	'Laravel\\Section' => SYS_PATH.'section'.EXT,
	'Laravel\\Session' => SYS_PATH.'session'.EXT,
	'Laravel\\Str' => SYS_PATH.'str'.EXT,
	'Laravel\\URI' => SYS_PATH.'uri'.EXT,
	'Laravel\\URL' => SYS_PATH.'url'.EXT,
	'Laravel\\Validator' => SYS_PATH.'validator'.EXT,
	'Laravel\\View' => SYS_PATH.'view'.EXT,

	'Laravel\\Cache\\Drivers\\APC' => SYS_PATH.'cache/drivers/apc'.EXT,
	'Laravel\\Cache\\Drivers\\Driver' => SYS_PATH.'cache/drivers/driver'.EXT,
	'Laravel\\Cache\\Drivers\\File' => SYS_PATH.'cache/drivers/file'.EXT,
	'Laravel\\Cache\\Drivers\\Memcached' => SYS_PATH.'cache/drivers/memcached'.EXT,
	'Laravel\\Cache\\Drivers\\Redis' => SYS_PATH.'cache/drivers/redis'.EXT,
	'Laravel\\Cache\\Drivers\\Database' => SYS_PATH.'cache/drivers/database'.EXT,

	'Laravel\\CLI\\Console' => SYS_PATH.'cli/console'.EXT,
	'Laravel\\CLI\\Command' => SYS_PATH.'cli/command'.EXT,
	'Laravel\\CLI\\Tasks\\Task' => SYS_PATH.'cli/tasks/task'.EXT,
	'Laravel\\CLI\\Tasks\\Bundle\\Bundler' => SYS_PATH.'cli/tasks/bundle/bundler'.EXT,
	'Laravel\\CLI\\Tasks\\Bundle\\Repository' => SYS_PATH.'cli/tasks/bundle/repository'.EXT,
	'Laravel\\CLI\\Tasks\\Bundle\\Publisher' => SYS_PATH.'cli/tasks/bundle/publisher'.EXT,
	'Laravel\\CLI\\Tasks\\Bundle\\Providers\\Provider' => SYS_PATH.'cli/tasks/bundle/providers/provider'.EXT,
	'Laravel\\CLI\\Tasks\\Bundle\\Providers\\Github' => SYS_PATH.'cli/tasks/bundle/providers/github'.EXT,
	'Laravel\\CLI\\Tasks\\Migrate\\Migrator' => SYS_PATH.'cli/tasks/migrate/migrator'.EXT,
	'Laravel\\CLI\\Tasks\\Migrate\\Resolver' => SYS_PATH.'cli/tasks/migrate/resolver'.EXT,
	'Laravel\\CLI\\Tasks\\Migrate\\Database' => SYS_PATH.'cli/tasks/migrate/database'.EXT,
	'Laravel\\CLI\\Tasks\\Key' => SYS_PATH.'cli/tasks/key'.EXT,
	'Laravel\\CLI\\Tasks\\Session\\Manager' => SYS_PATH.'cli/tasks/session/manager'.EXT,

	'Laravel\\Database\\Connection' => SYS_PATH.'database/connection'.EXT,
	'Laravel\\Database\\Expression' => SYS_PATH.'database/expression'.EXT,
	'Laravel\\Database\\Query' => SYS_PATH.'database/query'.EXT,
	'Laravel\\Database\\Schema' => SYS_PATH.'database/schema'.EXT,
	'Laravel\\Database\\Grammar' => SYS_PATH.'database/grammar'.EXT,
	'Laravel\\Database\\Connectors\\Connector' => SYS_PATH.'database/connectors/connector'.EXT,
	'Laravel\\Database\\Connectors\\MySQL' => SYS_PATH.'database/connectors/mysql'.EXT,
	'Laravel\\Database\\Connectors\\Postgres' => SYS_PATH.'database/connectors/postgres'.EXT,
	'Laravel\\Database\\Connectors\\SQLite' => SYS_PATH.'database/connectors/sqlite'.EXT,
	'Laravel\\Database\\Connectors\\SQLServer' => SYS_PATH.'database/connectors/sqlserver'.EXT,
	'Laravel\\Database\\Query\\Grammars\\Grammar' => SYS_PATH.'database/query/grammars/grammar'.EXT,
	'Laravel\\Database\\Query\\Grammars\\MySQL' => SYS_PATH.'database/query/grammars/mysql'.EXT,
	'Laravel\\Database\\Query\\Grammars\\SQLServer' => SYS_PATH.'database/query/grammars/sqlserver'.EXT,
	'Laravel\\Database\\Schema\\Table' => SYS_PATH.'database/schema/table'.EXT,
	'Laravel\\Database\\Schema\\Grammars\\Grammar' => SYS_PATH.'database/schema/grammars/grammar'.EXT,
	'Laravel\\Database\\Schema\\Grammars\\MySQL' => SYS_PATH.'database/schema/grammars/mysql'.EXT,
	'Laravel\\Database\\Schema\\Grammars\\Postgres' => SYS_PATH.'database/schema/grammars/postgres'.EXT,
	'Laravel\\Database\\Schema\\Grammars\\SQLServer' => SYS_PATH.'database/schema/grammars/sqlserver'.EXT,
	'Laravel\\Database\\Schema\\Grammars\\SQLite' => SYS_PATH.'database/schema/grammars/sqlite'.EXT,

	'Laravel\\Routing\\Controller' => SYS_PATH.'routing/controller'.EXT,
	'Laravel\\Routing\\Filter' => SYS_PATH.'routing/filter'.EXT,
	'Laravel\\Routing\\Filter_Collection' => SYS_PATH.'routing/filter'.EXT,
	'Laravel\\Routing\\Route' => SYS_PATH.'routing/route'.EXT,
	'Laravel\\Routing\\Router' => SYS_PATH.'routing/router'.EXT,

	'Laravel\\Session\\Payload' => SYS_PATH.'session/payload'.EXT,
	'Laravel\\Session\\Drivers\\APC' => SYS_PATH.'session/drivers/apc'.EXT,
	'Laravel\\Session\\Drivers\\Cookie' => SYS_PATH.'session/drivers/cookie'.EXT,
	'Laravel\\Session\\Drivers\\Database' => SYS_PATH.'session/drivers/database'.EXT,
	'Laravel\\Session\\Drivers\\Driver' => SYS_PATH.'session/drivers/driver'.EXT,
	'Laravel\\Session\\Drivers\\File' => SYS_PATH.'session/drivers/file'.EXT,
	'Laravel\\Session\\Drivers\\Memcached' => SYS_PATH.'session/drivers/memcached'.EXT,
	'Laravel\\Session\\Drivers\\Redis' => SYS_PATH.'session/drivers/redis'.EXT,
	'Laravel\\Session\\Drivers\\Sweeper' => SYS_PATH.'session/drivers/sweeper'.EXT,
);

/**
 * Register all of the core class aliases. These aliases provide a
 * convenient way of working with the Laravel core classes without
 * having to worry about the namespacing. The developer is also
 * free to remove aliases when they extend core classes.
 */
Autoloader::$aliases = Config::get('application.aliases');