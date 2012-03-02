<?php namespace Laravel\CLI\Tasks;

use Laravel\Str;
use Laravel\IoC;

class Help extends Task {

	/**
	 * Show a global help message, or call the help() method
	 * of a task given as parameter.
	 *
	 * @param array $arguments
	 */
	public function run($arguments = array())
	{
		if(! count($arguments)) $this->_help();

		// if help param contains a method, and split it
		if(strstr($arguments[0], ':') and $parts = explode(':', $arguments[0]))
		{
			$task = IoC::resolve('task: '. Str::lower($parts[0]));
			$help = 'help_'.Str::lower($parts[1]);
		}
		else
		{
			$task = IoC::resolve('task: '. Str::lower($arguments[0]));
			$help = 'help';
		}

		if(is_callable(array($task, $help)))
		{
			$task->$help();
		}
		else
		{
			echo "No documentation exists for this task." . PHP_EOL;
		}
	}

	private function _help()
	{
		echo "This will be the main help dialog." . PHP_EOL;
		exit();
	}

}
