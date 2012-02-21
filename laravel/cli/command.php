<?php namespace Laravel\CLI;

use Laravel\IoC;
use Laravel\Str;
use Laravel\Bundle;

class Command {

	/**
	 * Run a CLI task with the given arguments.
	 *
	 * <code>
	 *		// Call the migrate artisan task
	 *		Command::run(array('migrate'));
	 *
	 *		// Call the migrate task with some arguments
	 *		Command::run(array('migrate:rollback', 'bundle-name'))
	 * </code>
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public static function run($arguments = array())
	{
		static::validate($arguments);

		list($bundle, $task, $method) = static::parse($arguments[0]);

		// If the task exists within a bundle, we will start the bundle so that any
		// dependencies can be registered in the application IoC container. If the
		// task is registered in the container,  we'll resolve it.
		if (Bundle::exists($bundle)) Bundle::start($bundle);

		$task = static::resolve($bundle, $task);

		// Once the bundle has been resolved, we'll make sure we could actually
		// find that task, and then verify that the method exists on the task
		// so we can successfully call it without a problem.
		if (is_null($task))
		{
			throw new \Exception("Sorry, I can't find that task.");
		}

		if(is_callable(array($task, $method)))
		{
			$task->$method(array_slice($arguments, 1));
		}
		else
		{
			throw new \Exception("Sorry, I can't find that method!");
		}
	}

	/**
	 * Determine if the given command arguments are valid.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	protected static function validate($arguments)
	{
		if ( ! isset($arguments[0]))
		{
			throw new \Exception("You forgot to provide the task name.");
		}
	}

	/**
	 * Parse the task name to extract the bundle, task, and method.
	 *
	 * @param  string  $task
	 * @return array
	 */
	protected static function parse($task)
	{
		list($bundle, $task) = Bundle::parse($task);

		// Extract the task method from the task string. Methods are called
		// on tasks by separating the task and method with a single colon.
		// If no task is specified, "run" is used as the default.
		if (str_contains($task, ':'))
		{
			list($task, $method) = explode(':', $task);
		}
		else
		{
			$method = 'run';
		}

		return array($bundle, $task, $method);
	}

	/**
	 * Resolve an instance of the given task name.
	 *
	 * <code>
	 *		// Resolve an instance of a task
	 *		$task = Command::resolve('application', 'migrate');
	 *
	 *		// Resolve an instance of a task wtihin a bundle
	 *		$task = Command::resolve('bundle', 'foo');
	 * </code>
	 *
	 * @param  string  $bundle
	 * @param  string  $task
	 * @return object
	 */
	public static function resolve($bundle, $task)
	{
		$identifier = Bundle::identifier($bundle, $task);

		// First we'll check to see if the task has been registered in the
		// application IoC container. This allows all dependencies to be
		// injected into tasks for more testability.
		if (IoC::registered("task: {$identifier}"))
		{
			return IoC::resolve("task: {$identifier}");
		}

		// If the task file exists, we'll format the bundle and task name
		// into a task class name and resolve an instance of the so that
		// the requested method may be executed.
		if (file_exists($path = Bundle::path($bundle).'tasks/'.$task.EXT))
		{
			require $path;

			$task = static::format($bundle, $task);

			return new $task;
		}
	}

	/**
	 * Parse the command line arguments and return the results.
	 *
	 * @param  array  $argv
	 * @return array
	 */
	public static function options($argv)
	{
		$options = array();

		$arguments = array();

		for ($i = 0, $count = count($argv); $i < $count; $i++)
		{
			$argument = $argv[$i];

			// If the CLI argument starts with a double hyphen, it is an option,
			// so we will extract the value and add it to the array of options
			// to be returned by the method.
			if (starts_with($argument, '--'))
			{
				// By default, we will assume the value of the options is true,
				// but if the option contains an equals sign, we will take the
				// value to the right of the equals sign as the value and
				// remove the value from the option key.
				list($key, $value) = array(substr($argument, 2), true);

				if (($equals = strpos($argument, '=')) !== false)
				{
					$key = substr($argument, 2, $equals - 2);

					$value = substr($argument, $equals + 1);
				}

				$options[$key] = $value;
			}
			// If the CLI argument does not start with a double hyphen it's
			// simply an argument to be passed to the console task so we'll
			// add it to the array of "regular" arguments.
			else
			{
				$arguments[] = $argument;
			}
		}

		return array($arguments, $options);
	}

	/**
	 * Format a bundle and task into a task class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $task
	 * @return string
	 */
	protected static function format($bundle, $task)
	{
		$prefix = Bundle::class_prefix($bundle);

		return '\\'.$prefix.Str::classify($task).'_Task';
	}

}
