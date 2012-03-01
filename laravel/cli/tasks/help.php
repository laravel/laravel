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

		$task = IoC::resolve('task: '. $arguments[0]);

		if(is_callable(array($task, 'help')))
		{
			$task->help();
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