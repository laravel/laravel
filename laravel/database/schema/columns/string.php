<?php namespace Laravel\Database\Schema\Columns;

class String extends Column {

	/**
	 * The name of the column.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The length of the column.
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Create a new varchar column instance.
	 *
	 * @param  string  $name
	 * @param  int     $length
	 * @return void
	 */
	public function __construct($name, $length = 200)
	{
		$this->name = $name;
		$this->length = $length;
	}

}