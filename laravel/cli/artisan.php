<?php namespace Laravel\CLI; defined('DS') or die('No direct script access.');

use Laravel\Bundle;
use Laravel\Config;
use Laravel\Request;

/**
 * Fire up the default bundle. This will ensure any dependencies that
 * need to be registered in the IoC container are registered and that
 * the auto-loader mappings are registered.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * The default database connection may be set by specifying a value
 * for the "database" CLI option. This allows migrations to be run
 * conveniently for a test or staging database.
 */
if (isset($_SERVER['CLI']['DB']))
{
	Config::set('database.default', $_SERVER['CLI']['DB']);
}

/**
 * Overwrite the HttpFoundation request since we have set some of
 * the server variables since it was created. This allows us to
 * set the default database for the CLI task.
 */

use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;

Request::$foundation = RequestFoundation::createFromGlobals();

/**
 * We will register all of the Laravel provided tasks inside the IoC
 * container so they can be resolved by the task class. This allows
 * us to seamlessly add tasks to the CLI so that the Task class
 * doesn't have to worry about how to resolve core tasks.
 */
require path('sys').'cli/dependencies'.EXT;

/**
 * We will wrap the command execution in a try / catch block and
 * simply write out any exception messages we receive to the CLI
 * for the developer. Note that this only writes out messages
 * for the CLI exceptions. All others will be not be caught
 * and will be totally dumped out to the CLI.
 */
try
{
	Command::run(array_slice($arguments, 1));
}
catch (\Exception $e)
{
	echo $e->getMessage();
}

echo PHP_EOL;