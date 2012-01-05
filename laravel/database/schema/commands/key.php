<?php namespace Laravel\Database\Schema\Commands;

abstract class Key extends Command {

	/**
	 * The name of the key.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The columns for the key.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Create a new key command instance.
	 *
	 * @param  array   $columns
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($columns, $name = null)
	{
		$this->name = $name;
		$this->columns = (array) $columns;
	}

	/**
	 * Set the name on the key instance.
	 *
	 * @param  string  $name
	 * @return Key
	 */
	public function name($name)
	{
		$this->name = $name;
		return $this;
	}

}