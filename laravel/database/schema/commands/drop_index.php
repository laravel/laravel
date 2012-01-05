<?php namespace Laravel\Database\Schema\Commands;

class Drop_Index extends Command {

	/**
	 * The name of the index to be dropped.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Create a new instance of the drop index command.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

}