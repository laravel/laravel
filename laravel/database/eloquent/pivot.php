<?php namespace Laravel\Database\Eloquent;

class Pivot extends Model {

	/**
	 * The name of the pivot table's table.
	 *
	 * @var string
	 */
	protected $pivot_table;

	/**
	 * The database connection used for this model.
	 *
	 * @var Laravel\Database\Connection
	 */
	protected $pivot_connection;

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
	 * @param  string  $connection
	 * @return void
	 */
	public function __construct($table, $connection = null)
	{
		$this->pivot_table = $table;
		$this->pivot_connection = $connection;

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

	/**
	 * Get the connection used by the pivot table.
	 *
	 * @return string
	 */
	public function connection()
	{
		return $this->pivot_connection;
	}

}