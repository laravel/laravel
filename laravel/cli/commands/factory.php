<?php namespace Laravel\CLI\Commands;

use Laravel\IoC;

class Factory {

	/**
	 * Create an implementation of the CLI command interface.
	 *
	 * @param  string   $command
	 * @return Command
	 */
	public static function make($command)
	{
		switch ($command)
		{
			case 'task':
				return new Task;

			case 'bundle':
				return new Bundle(new \Laravel\CLI\Bundle\Repository);
		}
	}

}