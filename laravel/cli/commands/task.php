<?php namespace Laravel\CLI\Commands;

class Task implements Command {

	public function run($arguments = array())
	{
		var_dump($arguments);
	}

}