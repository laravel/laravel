<?php namespace Laravel\Database;

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
	 * @param  Schema\Table  $schema
	 * @return void
	 */
	public static function execute($schema)
	{
		if (count($schema->commands) == 0 and count($schema->columns) > 0)
		{
			$schema->commands[] = new Schema\Commands\Alter($schema);
		}

		foreach ($schema->commands as $command)
		{
			$grammar = static::grammar('mysql');

			//$connection = DB::connection($schema->connection);
			var_dump($grammar->{$command['type']}($command['table'], $command));
			echo '<br><br>';
			//$connection->query($grammar->{$command['type']}($command));
		}
		die;
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

			default:
				throw new \Exception("Schema operations not supported for [$driver].");
		}
	}

}