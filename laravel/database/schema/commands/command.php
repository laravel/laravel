<?php namespace Laravel\Database\Schema\Commands;

abstract class Command {

	/**
	 * Get the type of the command instance.
	 *
	 * @return string
	 */
	public function type()
	{
		return strtolower(basename(get_class($this)));
	}

}