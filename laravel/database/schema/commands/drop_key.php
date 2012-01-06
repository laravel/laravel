<?php namespace Laravel\Database\Schema\Commands;

abstract class Drop_Key extends Command {

	/**
	 * The name of the key to be dropped.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Create a new instance of the drop key command.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

}