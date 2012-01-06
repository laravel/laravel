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
		call_user_func($callback, $schema = new Schema\Table($table));

		return static::execute($schema);
	}

	/**
	 * Execute the given schema operation against the database.
	 *
	 * @param  Schema\Table  $table
	 * @return void
	 */
	public static function execute($table)
	{
		// If the developer has specified columns for the table and the
		// table is not being created, we will assume they simply want
		// to add the columns to the table, and will generate an add
		// command for them, adding the columns to the command.
		if (count($table->columns) > 0 and ! $table->creating())
		{
			die('adding');
			$command = new Fluent(array('type' => 'add'));

			array_unshift($table->commands, $command);
		}

		foreach ($table->commands as $command)
		{
			$connection = DB::connection($table->connection);

			$grammar = static::grammar($connection->driver());

			// Each grammar has a function that corresponds to the command type
			// and is responsible for building that's commands SQL. This lets
			// the SQL generation stay very granular and makes it simply to
			// add new database systems to the schema system.
			if (method_exists($grammar, $method = $command->type))
			{
				$statements = $grammar->$method($table, $command);

				// Once we have the statements, we will cast them to an array even
				// though not all of the commands return an array. This is just in
				// case the command needs to run more than one query to do what
				// it needs to do what is requested by the developer.
				foreach ((array) $statements as $statement)
				{
					$connection->statement($statement);
				}
			}
		}
	}

	/**
	 * Create the appropriate schema grammar for the driver.
	 *
	 * @param  string   $driver
	 * @return Grammar
	 */
	public static function grammar($driver)
	{
		switch ($driver)
		{
			case 'mysql':
				return new Schema\Grammars\MySQL;

			case 'pgsql':
				return new Schema\Grammars\Postgres;

			case 'sqlite':
				return new Schema\Grammars\SQLite;

			case 'sqlsrv':
				return new Schema\Grammars\SQLServer;
		}

		throw new \Exception("Schema operations not supported for [$driver].");
	}

}