<?php namespace Laravel\CLI\Tasks;

use Laravel\Str;
use Laravel\IoC;

/**
 * The help class is responsible for showing a global help page, and
 * the ability to define task help pages, and method help pages for
 * other artisan tasks.
 *
 * @package  	Laravel
 * @author  	Dayle Rees <me@daylerees.com>
 * @copyright  	2012 Taylor Otwell
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 */
class Help extends Task {

	/**
	 * Show a global help message, or call the help() method
	 * of a task given as parameter.
	 *
	 * Defining a task help page :
	 *
	 * <code>
	 * public function help()
	 * {
	 * 		echo "This is my help page.".PHP_EOL;
	 * }
	 * </code>
	 *
	 * Defining a help page for a Task's method.
	 *
	 * <code>
	 * public function help_methodname()
	 * {
	 * 		echo "This is the help page for task:methodname.".PHP_EOL;
	 * }
	 * </code>
	 *
	 * Usage :
	 *
	 * <code>
	 * php artisan help mytask
	 * php artisan help mytask:methodname
	 * <code>
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

	/**
	 * Show a help dialog for artisan itself.
	 */
	private function _help()
	{
		echo "This will be the main help dialog." . PHP_EOL;
		exit();
	}

}
