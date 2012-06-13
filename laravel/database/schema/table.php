<?php namespace Laravel\Database\Schema;

use Laravel\Fluent;

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
	 * The engine that should be used for the table.
	 *
	 * @var string
	 */
	public $engine;

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
	 * @return Fluent
	 */
	public function create()
	{
		return $this->command(__FUNCTION__);
	}

	/**
	 * Create a new primary key on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return Fluent
	 */
	public function primary($columns, $name = null)
	{
		return $this->key(__FUNCTION__, $columns, $name);
	}

	/**
	 * Create a new unique index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return Fluent
	 */
	public function unique($columns, $name = null)
	{
		return $this->key(__FUNCTION__, $columns, $name);
	}

	/**
	 * Create a new full-text index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return Fluent
	 */
	public function fulltext($columns, $name = null)
	{
		return $this->key(__FUNCTION__, $columns, $name);
	}

	/**
	 * Create a new index on the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return Fluent
	 */
	public function index($columns, $name = null)
	{
		return $this->key(__FUNCTION__, $columns, $name);
	}

	/**
	 * Add a foreign key constraint to the table.
	 *
	 * @param  string|array  $columns
	 * @param  string        $name
	 */
	public function foreign($columns, $name = null)
	{
		return $this->key(__FUNCTION__, $columns, $name);
	}

	/**
	 * Create a command for creating any index.
	 *
	 * @param  string        $type
	 * @param  string|array  $columns
	 * @param  string        $name
	 * @return Fluent
	 */
	public function key($type, $columns, $name)
	{
		$columns = (array) $columns;

		// If no index name was specified, we will concatenate the columns and
		// append the index type to the name to generate a unique name for
		// the index that can be used when dropping indexes.
		if (is_null($name))
		{
			$name = str_replace(array('-', '.'), '_', $this->name);

			$name = $name.'_'.implode('_', $columns).'_'.$type;
		}

		return $this->command($type, compact('name', 'columns'));
	}

	/**
	 * Drop the database table.
	 *
	 * @return Fluent
	 */
	public function drop()
	{
		return $this->command(__FUNCTION__);
	}

	/**
	 * Drop a column from the table.
	 *
	 * @param  string|array  $columns
	 * @return void
	 */
	public function drop_column($columns)
	{
		return $this->command(__FUNCTION__, array('columns' => (array) $columns));
	}

	/**
	 * Drop a primary key from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_primary($name = null)
	{
		return $this->drop_key(__FUNCTION__, $name);
	}

	/**
	 * Drop a unique index from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_unique($name)
	{
		return $this->drop_key(__FUNCTION__, $name);
	}

	/**
	 * Drop a full-text index from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_fulltext($name)
	{
		return $this->drop_key(__FUNCTION__, $name);
	}

	/**
	 * Drop an index from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_index($name)
	{
		return $this->drop_key(__FUNCTION__, $name);
	}

	/**
	 * Drop a foreign key constraint from the table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function drop_foreign($name)
	{
		return $this->drop_key(__FUNCTION__, $name);
	}

	/**
	 * Create a command to drop any type of index.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @return Fluent
	 */
	protected function drop_key($type, $name)
	{
		return $this->command($type, compact('name'));
	}

	/**
	 * Add an auto-incrementing integer to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function increments($name)
	{
		return $this->integer($name, true);
	}

	/**
	 * Add a string column to the table.
	 *
	 * @param  string  $name
	 * @param  int     $length
	 * @return Fluent
	 */
	public function string($name, $length = 200)
	{
		return $this->column(__FUNCTION__, compact('name', 'length'));
	}

	/**
	 * Add an integer column to the table.
	 *
	 * @param  string  $name
	 * @param  bool    $increment
	 * @return Fluent
	 */
	public function integer($name, $increment = false)
	{
		return $this->column(__FUNCTION__, compact('name', 'increment'));
	}

	/**
	 * Add a float column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function float($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
	}

	/**
	 * Add a decimal column to the table.
	 *
	 * @param  string  $name
	 * @param  int     $precision
	 * @param  int     $scale
	 * @return Fluent
	 */
	public function decimal($name, $precision, $scale)
	{
		return $this->column(__FUNCTION__, compact('name', 'precision', 'scale'));
	}

	/**
	 * Add a boolean column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function boolean($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
	}

	/**
	 * Create date-time columns for creation and update timestamps.
	 *
	 * @return void
	 */
	public function timestamps()
	{
		$this->date('created_at');

		$this->date('updated_at');
	}

	/**
	 * Add a date-time column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function date($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
	}

	/**
	 * Add a timestamp column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function timestamp($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
	}

	/**
	 * Add a text column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function text($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
	}

	/**
	 * Add a blob column to the table.
	 *
	 * @param  string  $name
	 * @return Fluent
	 */
	public function blob($name)
	{
		return $this->column(__FUNCTION__, compact('name'));
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
			return $value->type == 'create';
		}));
	}

	/**
	 * Create a new fluent command instance.
	 *
	 * @param  string  $type
	 * @param  array   $parameters
	 * @return Fluent
	 */
	protected function command($type, $parameters = array())
	{
		$parameters = array_merge(compact('type'), $parameters);

		$this->commands[] = new Fluent($parameters);

		return end($this->commands);
	}

	/**
	 * Create a new fluent column instance.
	 *
	 * @param  string  $type
	 * @param  array   $parameters
	 * @return Fluent
	 */
	protected function column($type, $parameters = array())
	{
		$parameters = array_merge(compact('type'), $parameters);

		$this->columns[] = new Fluent($parameters);

		return end($this->columns);
	}

}