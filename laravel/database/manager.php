<?php namespace Laravel\Database;

use Laravel\Config;

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * The configuration manager instance.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Connector  $connector
	 * @return void
	 */
	public function __construct(Config $config)
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
		if (is_null($connection)) $connection = $this->config->get('database.default');

		if ( ! array_key_exists($connection, $this->connections))
		{
			$config = $this->config->get("database.connections.{$connection}");

			if (is_null($config))
			{
				throw new \Exception("Database connection configuration is not defined for connection [$connection].");
			}

			$this->connections[$connection] = new Connection($this->connect($config), $config);
		}

		return $this->connections[$connection];
	}

	/**
	 * Get a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	protected function connect($config)
	{
		if (isset($config['connector'])) { return call_user_func($config['connector'], $config); }

		switch ($config['driver'])
		{
			case 'sqlite':
				$connector = new Connectors\SQLite;
				break;

			case 'mysql':
				$connector = new Connectors\MySQL;
				break;

			case 'pgsql':
				$connector = new Connectors\Postgres;
				break;

			default:
				throw new \Exception("Database driver [{$config['driver']}] is not supported.");
		}

		return $connector->connect($config);
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