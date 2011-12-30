<?php namespace Laravel\CLI; defined('APP_PATH') or die('No direct script access.');

use Laravel\Bundle;

/**
 * Fire up the default bundle. This will ensure any dependencies that
 * need to be registered in the IoC container are registered and that
 * the auto-loader mappings are registered.
 */
Bundle::start(DEFAULT_BUNDLE);

/**
 * First we need to create an implementation of a Laravel CLI command.
 * All of the CLI commands implement the "Command" interface, and the
 * factory will return an implementation based on the type of command
 * it is given, which should be the second CLI argument.
 */
$command = Commands\Factory::make(array_get($_SERVER['argv'], 1));

/**
 * If we received a command implementation from the factory, we can
 * go ahead and execute the command, giving it hte parameters, sans
 * the CLI and script name.
 *
 * We will wrap the command execution in a try / catch block and
 * simply write out any exception messages we receive to the CLI
 * for the developer. Note that this only writes out messages
 * for the CLI exceptions. All others will be not be caught
 * and will be totally dumped out to the CLI.
 */
if ( ! is_null($command))
{
	try
	{
		$command->run(array_slice($_SERVER['argv'], 2));
	}
	catch (\Exception $e)
	{
		echo $e->getMessage();
	}
}
/**
 * If we didn't receive a command implementation, that command
 * is not currently supported by the CLI so we will just throw
 * out an error message to the developer.
 */
else
{
	echo "Sorry, I don't know how to do that.";
}

echo PHP_EOL;