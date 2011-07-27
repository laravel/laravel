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
	 * @param  string  $connection
	 * @return PDO
	 */
	public static function connection($connection = null)
	{
		if (is_null($connection))
		{
			$connection = Config::get('db.default');
		}

		if ( ! array_key_exists($connection, static::$connections))
		{
			static::$connections[$connection] = DB\Connector::connect($connection);
		}

		return static::$connections[$connection];
	}

	/**
	 * Execute a SQL query against the connection and return the first result.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  string  $connection
	 * @return object
	 */
	public static function first($sql, $bindings = array(), $connection = null)
	{
		return (count($results = static::query($sql, $bindings, $connection)) > 0) ? $results[0] : null;
	}

	/**
	 * Execute a SQL query against the connection.
	 *
	 * The method returns the following based on query type:
	 *
	 *     SELECT -> Array of stdClasses
	 *     UPDATE -> Number of rows affected.
	 *     DELETE -> Number of Rows affected.
	 *     ELSE   -> Boolean true / false depending on success.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  string  $connection
	 * @return array
	 */
	public static function query($sql, $bindings = array(), $connection = null)
	{
		$query = static::connection($connection)->prepare($sql);

		$result = $query->execute($bindings);

		if (strpos(strtoupper($sql), 'SELECT') === 0)
		{
			return $query->fetchAll(\PDO::FETCH_CLASS, 'stdClass');
		}
		elseif (strpos(strtoupper($sql), 'UPDATE') === 0 or strpos(strtoupper($sql), 'DELETE') === 0)
		{
			return $query->rowCount();
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * This method is simply a convenient shortcut into Query::table.
	 *
	 * @param  string  $table
	 * @param  string  $connection
	 * @return Query
	 */
	public static function table($table, $connection = null)
	{
		return new DB\Query($table, $connection);
	}

	/**
	 * Get the driver name for a database connection.
	 *
	 * @param  string  $connection
	 * @return string
	 */
	public static function driver($connection = null)
	{
		return static::connection($connection)->getAttribute(\PDO::ATTR_DRIVER_NAME);
	}

	/**
	 * Get the table prefix for a database connection.
	 *
	 * @param  string  $connection
	 * @return string
	 */
	public static function prefix($connection = null)
	{
		$connections = Config::get('db.connections');

		if (is_null($connection))
		{
			$connection = Config::get('db.default');
		}

		return (array_key_exists('prefix', $connections[$connection])) ? $connections[$connection]['prefix'] : '';
	}

}