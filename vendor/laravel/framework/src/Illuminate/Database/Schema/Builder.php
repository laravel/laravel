<?php namespace Illuminate\Database\Schema;

use Closure;
use Illuminate\Database\Connection;

class Builder {

	/**
	 * The database connection instance.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * The schema grammar instance.
	 *
	 * @var \Illuminate\Database\Schema\Grammars\Grammar
	 */
	protected $grammar;

	/**
	 * The Blueprint resolver callback.
	 *
	 * @var \Closure
	 */
	protected $resolver;

	/**
	 * Create a new database Schema manager.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @return void
	 */
	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
		$this->grammar = $connection->getSchemaGrammar();
	}

	/**
	 * Determine if the given table exists.
	 *
	 * @param  string  $table
	 * @return bool
	 */
	public function hasTable($table)
	{
		$sql = $this->grammar->compileTableExists();

		$table = $this->connection->getTablePrefix().$table;

		return count($this->connection->select($sql, array($table))) > 0;
	}

	/**
	 * Determine if the given table has a given column.
	 *
	 * @param  string  $table
	 * @param  string  $column
	 * @return bool
	 */
	public function hasColumn($table, $column)
	{
		$column = strtolower($column);

		return in_array($column, array_map('strtolower', $this->getColumnListing($table)));
	}

	/**
	 * Get the column listing for a given table.
	 *
	 * @param  string  $table
	 * @return array
	 */
	public function getColumnListing($table)
	{
		$table = $this->connection->getTablePrefix().$table;

		$results = $this->connection->select($this->grammar->compileColumnExists($table));

		return $this->connection->getPostProcessor()->processColumnListing($results);
	}

	/**
	 * Modify a table on the schema.
	 *
	 * @param  string   $table
	 * @param  Closure  $callback
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function table($table, Closure $callback)
	{
		$this->build($this->createBlueprint($table, $callback));
	}

	/**
	 * Create a new table on the schema.
	 *
	 * @param  string   $table
	 * @param  Closure  $callback
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function create($table, Closure $callback)
	{
		$blueprint = $this->createBlueprint($table);

		$blueprint->create();

		$callback($blueprint);

		$this->build($blueprint);
	}

	/**
	 * Drop a table from the schema.
	 *
	 * @param  string  $table
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function drop($table)
	{
		$blueprint = $this->createBlueprint($table);

		$blueprint->drop();

		$this->build($blueprint);
	}

	/**
	 * Drop a table from the schema if it exists.
	 *
	 * @param  string  $table
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function dropIfExists($table)
	{
		$blueprint = $this->createBlueprint($table);

		$blueprint->dropIfExists();

		$this->build($blueprint);
	}

	/**
	 * Rename a table on the schema.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function rename($from, $to)
	{
		$blueprint = $this->createBlueprint($from);

		$blueprint->rename($to);

		$this->build($blueprint);
	}

	/**
	 * Execute the blueprint to build / modify the table.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @return void
	 */
	protected function build(Blueprint $blueprint)
	{
		$blueprint->build($this->connection, $this->grammar);
	}

	/**
	 * Create a new command set with a Closure.
	 *
	 * @param  string   $table
	 * @param  Closure  $callback
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	protected function createBlueprint($table, Closure $callback = null)
	{
		if (isset($this->resolver))
		{
			return call_user_func($this->resolver, $table, $callback);
		}
		else
		{
			return new Blueprint($table, $callback);
		}
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Set the database connection instance.
	 *
	 * @param  \Illuminate\Database\Connection
	 * @return \Illuminate\Database\Schema\Builder
	 */
	public function setConnection(Connection $connection)
	{
		$this->connection = $connection;

		return $this;
	}

	/**
	 * Set the Schema Blueprint resolver callback.
	 *
	 * @param  \Closure  $resolver
	 * @return void
	 */
	public function blueprintResolver(Closure $resolver)
	{
		$this->resolver = $resolver;
	}

}
