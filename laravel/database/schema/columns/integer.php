<?php namespace Laravel\Database\Schema\Columns;

class Integer extends Column {

	/**
	 * Indicates if the integer column should auto-increment.
	 *
	 * @var bool
	 */
	public $increment;

	/**
	 * Create a new instance of an integer column.
	 *
	 * @param  string  $name
	 * @param  bool    $increment
	 * @return void
	 */
	public function __construct($name, $increment = false)
	{
		$this->increment = $increment;

		parent::__construct($name);
	}

}