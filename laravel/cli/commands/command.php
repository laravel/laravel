<?php namespace Laravel\CLI\Commands;

interface Command {

	/**
	 * Run the command using the given arguments.
	 *
	 * @param  array  $arguments
	 * @return void
	 */
	public function run($arguments = array());

}