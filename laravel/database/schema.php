<?php namespace Laravel\Database;

use Laravel\Fluent;
use Laravel\Database as DB;

class Schema {

	/**
	 * Begin a fluent schema operation on a database table.
	 *
	 * @param  string   $table
	 * @param  Closure  $callback
	 * @return void
	 */
	public static function table($table, $callback)
	{
		call_user_func($callback, $table = new Schema\Table($table));

		static::implications($table);

		return static::execute($table);
	}

	/**
	 * Execute the given schema operation against the database.
	 *
	 * @param  Schema\Table  $table
	 * @return void
	 */
	public static function execute($table)
	{
		foreach ($table->commands as $command)
		{
			$connection = DB::connection($table->connection);

			$grammar = static::grammar($connection);

			// Each grammar has a function that corresponds to the command type and is for
			// building that command's SQL. This lets the SQL generation stay granular
			// and flexible across various database systems.
			if (method_exists($grammar, $method = $command->type))
			{
				$statements = $grammar->$method($table, $command);

				// Once we have the statements, we will cast them to an array even though
				// not all of the commands return an array just in case the command
				// needs multiple queries to complete its database work.
				foreach ((array) $statements as $statement)
				{
					$connection->statement($statement);
				}
			}
		}
	}

	/**
	 * Add any implicit commands to the schema table operation.
	 *
	 * @param   Schema\Table  $table
	 * @return  void
	 */
	protected static function implications($table)
	{
		// If the developer has specified columns for the table and the table is
		// not being created, we'll assume they simply want to add the columns
		// to the table and generate the add command.
		if (count($table->columns) > 0 and ! $table->creating())
		{
			$command = new Fluent(array('type' => 'add'));

			array_unshift($table->commands, $command);
		}

		// For some extra syntax sugar, we'll check for any implicit indexes
		// on the table since the developer may specify the index type on
		// the fluent column declaration for convenience.
		foreach ($table->columns as $column)
		{
			foreach (array('primary', 'unique', 'fulltext', 'index') as $key)
			{
				if (isset($column->attributes[$key]))
				{
					$table->$key($column->name, $column->$key);
				}
			}
		}
	}

	/**
	 * Create the appropriate schema grammar for the driver.
	 *
	 * @param  Connection  $connection
	 * @return Grammar
	 */
	public static function grammar(Connection $connection)
	{
		$driver = $connection->driver();

		switch ($driver)
		{
			case 'mysql':
				return new Schema\Grammars\MySQL($connection);

			case 'pgsql':
				return new Schema\Grammars\Postgres($connection);

			case 'sqlsrv':
				return new Schema\Grammars\SQLServer($connection);

			case 'sqlite':
				return new Schema\Grammars\SQLite($connection);
		}

		throw new \Exception("Schema operations not supported for [$driver].");
	}

}