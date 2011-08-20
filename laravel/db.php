<?php namespace Laravel;

class DB {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	public static $connections = array();

	/**
	 * Get a database connection. If no database name is specified, the default
	 * connection will be returned as defined in the db configuration file.
	 *
	 * Note: Database connections are managed as singletons.
	 *
	 * <code>
	 *		// Get the default database connection
	 *		$connection = DB::connection();
	 *
	 *		// Get a specific database connection
	 *		$connection = DB::connection('mysql');
	 * </code>
	 *
	 * @param  string         $connection
	 * @return DB\Connection
	 */
	public static function connection($connection = null)
	{
		if (is_null($connection)) $connection = Config::get('db.default');

		if ( ! array_key_exists($connection, static::$connections))
		{
			if (is_null($config = Config::get('db.connections.'.$connection)))
			{
				throw new \Exception("Database connection [$connection] is not defined.");
			}

			$connector = DB\Connector\Factory::make($config);

			static::$connections[$connection] = new DB\Connection($connection, $config, $connector);
		}

		return static::$connections[$connection];
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * This method primarily serves as a short-cut to the $connection->table() method.
	 *
	 * <code>
	 *		// Begin a fluent query against the "users" table
	 *		$query = DB::table('users');
	 *
	 *		// Equivalent call using the connection table method.
	 *		$query = DB::connection()->table('users');
	 *
	 *		// Begin a fluent query against the "users" table for a specific connection
	 *		$query = DB::table('users', 'mysql');
	 * </code>
	 *
	 * @param  string    $table
	 * @param  string    $connection
	 * @return DB\Query
	 */
	public static function table($table, $connection = null)
	{
		return static::connection($connection)->table($table);
	}

	/**
	 * Magic Method for calling methods on the default database connection.
	 *
	 * This provides a convenient API for querying or examining the default database connection.
	 *
	 * <code>
	 *		// Run a query against the default database connection
	 *		$results = DB::query('select * from users');
	 *
	 *		// Equivalent call using the connection instance
	 *		$results = DB::connection()->query('select * from users');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::connection(), $method), $parameters);
	}

}