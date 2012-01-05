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
		$this->commands[] = new Commands\Create;
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
	 * @param  string        $name
	 * @return void
	 */
	public function primary($columns, $name = null)
	{
		$this->commands[] = new Commands\Primary($columns, $name);
	}

	/**
	 * Create a new unique index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return void
	 */
	public function unique($columns, $name = null)
	{
		$this->commands[] = new Commands\Unique($columns, $name);
	}

	/**
	 * Create a new full-text index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return void
	 */
	public function fulltext($columns, $name = null)
	{
		$this->commands[] = new Commands\Fulltext($columns, $name);
	}

	/**
	 * Drop the database table.
	 *
	 * @return void
	 */
	public function drop()
	{
		$this->commands[] = new Commands\Drop;
	}

	/**
	 * Drop a column from the table.
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function drop_column($columns)
	{
		$this->commands[] = new Commands\Drop_Column($columns);
	}

	/**
	 * Drop an index from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_index($name)
	{
		$this->commands[] = new Commands\Drop_Index($name);
	}

	/**
	 * Create a new index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return void
	 */
	public function index($columns, $name = null)
	{
		$this->commands[] = new Commands\Index($columns, $name);
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
	 * @return Column
	 */
	public function integer($column, $increment = false)
	{
		$this->columns[] = new Columns\Integer($column, $increment);

		return end($this->columns);
	}

	/**
	 * Add a float column to the table.
	 *
	 * @param  string  $column
	 * @param  bool    $increment
	 * @return Column
	 */
	public function float($column)
	{
		$this->columns[] = new Columns\Float($column);

		return end($this->columns);
	}

	/**
	 * Add a boolean column to the table.
	 *
	 * @param  string  $column
	 * @return Column
	 */
	public function boolean($column)
	{
		$this->columns[] = new Columns\Boolean($column);

		return end($this->columns);
	}

	/**
	 * Add a date-time column to the table.
	 *
	 * @param  string  $column
	 * @return Column
	 */
	public function date($column)
	{
		$this->columns[] = new Columns\Date($column);

		return end($this->columns);
	}

	/**
	 * Add a timestamp column to the table.
	 *
	 * @param  string  $column
	 * @return Column
	 */
	public function timestamp($column)
	{
		$this->columns[] = new Columns\Timestamp($column);

		return end($this->columns);
	}

	/**
	 * Add a text column to the table.
	 *
	 * @param  string  $column
	 * @return Column
	 */
	public function text($column)
	{
		$this->columns[] = new Columns\Text($column);

		return end($this->columns);
	}

	/**
	 * Add a blob column to the table.
	 *
	 * @param  string  $column
	 * @return Column
	 */
	public function blob($column)
	{
		$this->columns[] = new Columns\Blob($column);

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

	/**
	 * Determine if the schema table has a creation command.
	 *
	 * @return bool
	 */
	public function creating()
	{
		return ! is_null(array_first($this->commands, function($key, $value)
		{
			return $value instanceof Commands\Create;
		}));
	}

}