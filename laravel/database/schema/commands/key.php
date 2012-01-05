<?php namespace Laravel\Database\Schema\Commands;

abstract class Key extends Command {

	/**
	 * The columns for the key.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Create a new key command instance.
	 *
	 * @param  array  $columns
	 * @return void
	 */
	public function __construct($columns)
	{
		$this->columns = (array) $columns;
	}

}