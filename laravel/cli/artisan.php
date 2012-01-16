<?php namespace Laravel\CLI; defined('APP_PATH') or die('No direct script access.');

use Laravel\IoC;
use Laravel\Bundle;
use Laravel\Database as DB;

/**
 * Fire up the default bundle. This will ensure any dependencies that
 * need to be registered in the IoC container are registered and that
 * the auto-loader mappings are registered.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * We will register all of the Laravel provided tasks inside the IoC
 * container so they can be resolved by the task class. This allows
 * us to seamlessly add tasks to the CLI so that the Task class
 * doesn't have to worry about how to resolve core tasks.
 */

/**
 * The bundle task is responsible for the installation of bundles
 * and their dependencies. It utilizes the bundles API to get the
 * meta-data for the available bundles.
 */
IoC::register('task: bundle', function()
{
	return new Tasks\Bundle\Bundler;
});

/**
 * The migrate task is responsible for running database migrations
 * as well as migration rollbacks. We will also create an instance
 * of the migration resolver and database classes, which are used
 * to perform various support functions for the migrator.
 */
IoC::register('task: migrate', function()
{
	$database = new Tasks\Migrate\Database;

	$resolver = new Tasks\Migrate\Resolver($database);

	return new Tasks\Migrate\Migrator($resolver, $database);
});

/**
 * We will wrap the command execution in a try / catch block and
 * simply write out any exception messages we receive to the CLI
 * for the developer. Note that this only writes out messages
 * for the CLI exceptions. All others will be not be caught
 * and will be totally dumped out to the CLI.
 */
try
{
	Command::run(array_slice($_SERVER['argv'], 1));
}
catch (\Exception $e)
{
	echo $e->getMessage();
}

echo PHP_EOL;