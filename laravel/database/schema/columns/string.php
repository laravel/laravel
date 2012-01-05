<?php namespace Laravel\Database\Schema\Columns;

class String extends Column {

	/**
	 * The maximum length of the column.
	 *
	 * @var int
	 */
	public $length;

	/**
	 * Create a new instance of the string column.
	 *
	 * @param  string  $name
	 * @param  int     $Length
	 * @return void
	 */
	public function __construct($name, $length = 200)
	{
		$this->length = $length;

		parent::__construct($name);
	}

}