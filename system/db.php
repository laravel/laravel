<?php namespace System;

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

			static::$connections[$connection] = new DB\Connection($connection, (object) $config, new DB\Connector);
		}

		return static::$connections[$connection];
	}

	/**
	 * Begin a fluent query against a table.
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
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::connection(), $method), $parameters);
	}

}