<?php namespace Laravel\Database;

use Laravel\IoC;
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
	 * <code>
	 *		// Get the default database connection for the application
	 *		$connection = DB::connection();
	 *
	 *		// Get a specific connection by passing the connection name
	 *		$connection = DB::connection('mysql');
	 * </code>
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
				throw new \Exception("Database connection is not defined for connection [$connection].");
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
		// We allow the developer to place a "connector" option in the database
		// configuration, which should have a Closure value. If the connector
		// is present, we will use the Closure to retrieve the PDO connection
		// to the database. This allows the flexiblity to connect to database
		// systems that are not officially supported by the the framework.
		if (isset($config['connector']))
		{
			return call_user_func($config['connector'], $config);
		}

		return IoC::container()->core("database.connectors.{$config['driver']}")->connect($config);
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
	 * Create a new database expression instance.
	 *
	 * Database expressions are used to inject raw SQL into a fluent query.
	 *
	 * @param  string      $value
	 * @return Expression
	 */
	public static function raw($value)
	{
		return new Expression($value);
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