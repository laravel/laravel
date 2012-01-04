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
		$this->commands[] = array('type' => 'create', 'table' => $this);
	}

	/**
	 * Create a new primary key on the table.
	 *
	 * <code>
	 *		// Add a new primary key to the table
	 *		$table->primary('email');
	 *
	 *		// Add a composite primary key to the table
	 *		$table->primary(array('firstname', 'lastname'));
	 * </code>
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function primary($columns)
	{
		$this->key(__FUNCTION__, $columns);
	}

	/**
	 * Create a new unique index on the table.
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function unique($columns)
	{
		$this->key(__FUNCTION__, $columns);
	}

	/**
	 * Create a new index on the table.
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function index($columns)
	{
		$this->key(__FUNCTION__, $columns);
	}

	/**
	 * Add an indexing command to the table.
	 *
	 * @param  string         $type
	 * @param  string|array   $columns
	 * @return void
	 */
	protected function key($type, $columns)
	{
		$columns = (array) $columns;

		$this->commands[] = array('type' => $type, 'table' => $this, 'columns' => $columns);
	}

	/**
	 * Add an auto-incrementing integer to the table.
	 *
	 * @param  string  $column
	 * @return void
	 */
	public function increments($column)
	{
		$this->integer($column, true);
	}

	/**
	 * Add a string column to the table.
	 *
	 * @param  string  $column
	 * @param  int     $length
	 * @return Column
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
	 * @param  bool    $increment
	 * @return void
	 */
	public function integer($column, $increment = false)
	{
		$this->columns[] = new Columns\Integer($column, $increment);

		return end($this->columns);
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