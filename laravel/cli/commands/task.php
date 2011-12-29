<?php namespace Laravel\CLI\Commands;

use Laravel\Bundle;

class Task implements Command {

	/**
	 * Run a Laravel CLI task with the given arguments.
	 *
	 * <code>
	 *		// Run the "notify" task
	 *		php laravel task notify
	 *
	 *		// Run the "notify" taks and pass a name into the task
	 *		php laravel task notify taylor
	 * </code>
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function run($arguments = array())
	{
		if ( ! isset($arguments[0]))
		{
			throw new \Exception("Whoops! You forgot to provide the task name.");
		}

		list($bundle, $task) = Bundle::parse($arguments[0]);

		if (Bundle::exists($bundle))
		{
			Bundle::start($bundle);
		}

		if ( ! is_null($task = $this->resolve($bundle, $task)))
		{
			$task->run(array_slice($arguments, 1));
		}
	}

	protected function resolve($bundle, $task)
	{
		if (file_exists($path = Bundle::path($bundle).'tasks/'.$task.EXT))
		{
			require $path;
		}
	}

}