<?php namespace Laravel\CLI; defined('DS') or die('No direct script access.');

use Laravel\Bundle;
use Laravel\Config;

/**
 * Fire up the default bundle. This will ensure any dependencies that
 * need to be registered in the IoC container are registered and that
 * the auto-loader mappings are registered.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * Set the CLI options on the $_SERVER global array so we can easily
 * retrieve them from the various parts of the CLI code. We can use
 * the Request class to access them conveniently.
 */
list($arguments, $_SERVER['CLI']) = Command::options($_SERVER['argv']);

$_SERVER['CLI'] = array_change_key_case($_SERVER['CLI'], CASE_UPPER);

/**
 * The Laravel environment may be specified on the CLI using the "env"
 * option, allowing the developer to easily use local configuration
 * files from the CLI since the environment is usually controlled
 * by server environmenet variables.
 */
if (isset($_SERVER['CLI']['ENV']))
{
	$_SERVER['LARAVEL_ENV'] = $_SERVER['CLI']['ENV'];
}

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