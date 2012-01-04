<?php namespace Laravel\Database\Schema;

class Table {

	/**
	 * The database table name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The database connection that should be used.
	 *
	 * @var string
	 */
	public $connection;

	/**
	 * The columns that should be added to the table.
	 *
	 * @var array
	 */
	public $columns = array();

	/**
	 * The commands that should be executed on the table.
	 *
	 * @var array
	 */
	public $commands = array();

	/**
	 * Create a new schema table instance.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Indicate that the table should be created.
	 *
	 * @return void
	 */
	public function create()
	{
		$this->commands[] = new Commands\Create($this);
	}

	/**
	 * Add a string column to the table.
	 *
	 * @param  string  $column
	 * @param  int     $length
	 * @return void
	 */
	public function string($column, $length = 200)
	{
		$this->columns[] = new Columns\String($column, $length);

		return end($this->columns);
	}

	/**
	 * Add an integer column to the table.
	 *
	 * @param  string  $column
	 * @return void
	 */
	public function integer($column)
	{
		$this->columns[] = new Columns\Integer($column);
	}

	/**
	 * Set the database connection for the table operation.
	 *
	 * @param  string  $connection
	 * @return void
	 */
	public function on($connection)
	{
		$this->connection = $connection;
	}

}