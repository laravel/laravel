<?php namespace Laravel\Database;

use Laravel\Config;

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	protected static $connections = array();

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
	public static function connection($connection = null)
	{
		if (is_null($connection)) $connection = Config::get('database.default');

		if ( ! array_key_exists($connection, static::$connections))
		{
			$config = Config::get("database.connections.{$connection}");

			if (is_null($config))
			{
				throw new \Exception("Database connection configuration is not defined for connection [$connection].");
			}

			static::$connections[$connection] = new Connection(static::connect($config), $config);
		}

		return static::$connections[$connection];
	}

	/**
	 * Get a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	protected static function connect($config)
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
	public static function table($table, $connection = null)
	{
		return static::connection($connection)->table($table);
	}

	/**
	 * Magic Method for calling methods on the default database connection.
	 *
	 * This provides a convenient API for querying or examining the default database connection.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::connection(), $method), $parameters);
	}

}