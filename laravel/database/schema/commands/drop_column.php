<?php namespace Laravel\Database\Schema\Commands;

class Drop_Column extends Command {

	/**
	 * The columns that should be dropped.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Create a new instance of the drop column command.
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function __construct($columns)
	{
		$this->columns = (array) $columns;
	}

}