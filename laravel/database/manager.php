<?php namespace Laravel\Database;

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	public $connections = array();

	/**
	 * The database connection configurations.
	 *
	 * @var array
	 */
	private $config;

	/**
	 * The default database connection name.
	 *
	 * @var string
	 */
	private $default;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  string  $default
	 */
	public function __construct($config, $default)
	{
		$this->config = $config;
		$this->default = $default;
	}

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
	 * @param  string               $connection
	 * @return Database\Connection
	 */
	public function connection($connection = null)
	{
		if (is_null($connection)) $connection = $this->default;

		if ( ! array_key_exists($connection, $this->connections))
		{
			if ( ! isset($this->config[$connection]))
			{
				throw new \Exception("Database connection [$connection] is not defined.");
			}

			$connector = Connector\Factory::make($this->config[$connection]);

			static::$connections[$connection] = new Connection($connection, $this->config[$connection], $connector);
		}

		return $this->connections[$connection];
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
	 * @return Database\Query
	 */
	public function table($table, $connection = null)
	{
		return $this->connection($connection)->table($table);
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
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}