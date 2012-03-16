<?php namespace Laravel\Database\Eloquent;

class Pivot extends Model {

	/**
	 * The name of the pivot table's table.
	 *
	 * @var string
	 */
	public $pivot_table;

	/**
	 * Indicates if the model has update and creation timestamps.
	 *
	 * @var bool
	 */
	public static $timestamps = true;

	/**
	 * Create a new pivot table instance.
	 *
	 * @param  string  $table
	 * @return void
	 */
	public function __construct($table)
	{
		$this->pivot_table = $table;

		parent::__construct(array(), true);
	}

	/**
	 * Get the name of the pivot table.
	 *
	 * @return string
	 */
	public function table()
	{
		return $this->pivot_table;
	}

}