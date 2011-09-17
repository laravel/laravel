<?php namespace Laravel\Database;

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Create a new database manager instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Get a database connection.
	 *
	 * If no database name is specified, the default connection will be returned.
	 *
	 * Note: Database connections are managed as singletons.
	 *
	 * @param  string      $connection
	 * @return Connection
	 */
	public function connection($connection = null)
	{
		if (is_null($connection)) $connection = $this->config['default'];

		if ( ! array_key_exists($connection, $this->connections))
		{
			if ( ! isset($this->config['connectors'][$connection]))
			{
				throw new \Exception("Database connection configuration is not defined for connection [$connection].");
			}

			// Database connections are established by developer configurable connector closures.
			// This provides the developer the maximum amount of freedom in establishing their
			// database connections, and allows the framework to remain agonstic to ugly database
			// specific PDO connection details. Less code. Less bugs.
			$pdo = call_user_func($this->config['connectors'][$connection]);

			$this->connections[$connection] = new Connection($pdo, $this->config));
		}

		return $this->connections[$connection];
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * @param  string          $table
	 * @param  string          $connection
	 * @return Queries\Query
	 */
	public function table($table, $connection = null)
	{
		return $this->connection($connection)->table($table);
	}

	/**
	 * Magic Method for calling methods on the default database connection.
	 *
	 * This provides a convenient API for querying or examining the default database connection.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}