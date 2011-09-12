<?php namespace Laravel\Database;

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * The connector factory instance.
	 *
	 * @var Connector\Factory
	 */
	protected $connector;

	/**
	 * The database connection configurations.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The default database connection name.
	 *
	 * @var string
	 */
	protected $default;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Connector\Factory  $connector
	 * @param  array              $config
	 * @param  string             $default
	 * @return void
	 */
	public function __construct(Connector\Factory $connector, $config, $default)
	{
		$this->config = $config;
		$this->default = $default;
		$this->connector = $connector;
	}

	/**
	 * Get a database connection. If no database name is specified, the default
	 * connection will be returned as defined in the database configuration file.
	 *
	 * Note: Database connections are managed as singletons.
	 *
	 * <code>
	 *		// Get the default database connection
	 *		$connection = DB::connection();
	 *
	 *		// Get a database connection by name
	 *		$connection = DB::connection('slave');
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

			list($connector, $query, $compiler) = array($this->connector->make($this->config[$connection]), new Query\Factory, new Query\Compiler\Factory);

			$this->connections[$connection] = new Connection($connector, $query, $compiler, $connection, $this->config[$connection]);
		}

		return $this->connections[$connection];
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * <code>
	 *		// Begin a fluent query against the "users" table using the default connection
	 *		$query = DB::table('users');
	 *
	 *		// Begin a fluent query against the "users" table using a specified connection
	 *		$query = DB::table('users', 'slave');
	 * </code>
	 *
	 * @param  string          $table
	 * @param  string          $connection
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
	 *		// Perform a query against the default connection
	 *		$results = DB::query('select * from users');
	 *
	 *		// Get the name of the PDO driver being used by the default connection
	 *		$driver = DB::driver();
	 * </code>
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}