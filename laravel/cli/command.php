<?php namespace Laravel\CLI;

use Laravel\IoC;
use Laravel\Str;
use Laravel\Bundle;

class Command {

	/**
	 * Run a CLI task with the given arguments.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public static function run($arguments = array())
	{
		if ( ! isset($arguments[0]))
		{
			throw new \Exception("Whoops! You forgot to provide the task name.");
		}

		list($bundle, $task, $method) = static::parse($arguments[0]);

		// If the task exists within a bundle, we will start the bundle so that
		// any dependencies can be registered in the application IoC container.
		// If the task is registered in the container, it will be resolved
		// via the container instead of by this class.
		if (Bundle::exists($bundle)) Bundle::start($bundle);

		if (is_null($task = static::resolve($bundle, $task)))
		{
			throw new \Exception("Sorry, I can't find that task.");
		}

		$task->$method(array_slice($arguments, 1));
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
		// If no task is specified, "run" is used as the default method.
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
	 * @param  string  $bundle
	 * @param  string  $task
	 * @return object
	 */
	public static function resolve($bundle, $task)
	{
		$identifier = Bundle::identifier($bundle, $task);

		// First we'll check to see if the task has been registered in
		// the application IoC container. This allows dependencies to
		// be injected into tasks for more testability.
		if (IoC::registered("task: {$identifier}"))
		{
			return IoC::resolve("task: {$identifier}");
		}

		// If the task file exists, we'll format the bundle and task
		// name into a task class name and resolve an instance of
		// the so that the requested method may be executed.
		if (file_exists($path = Bundle::path($bundle).'tasks/'.$task.EXT))
		{
			require $path;

			$task = static::format($bundle, $task);

			return new $task;
		}
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

		return '\\'.$prefix.Str::clasify($task).'_Task';
	}

}