<?php namespace System;

class DB {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	private static $connections = array();

	/**
	 * Get a database connection.
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

		// ---------------------------------------------------
		// If we have already established this connection,
		// simply return the existing connection.
		//
		// Don't want to establish the same connection twice!
		// ---------------------------------------------------
		if ( ! array_key_exists($connection, static::$connections))
		{
			$config = Config::get('db.connections');

			if ( ! array_key_exists($connection, $config))
			{
				throw new \Exception("Database connection [$connection] is not defined.");
			}

			static::$connections[$connection] = DB\Connector::connect((object) $config[$connection]);
		}

		return static::$connections[$connection];
	}

	/**
	 * Execute a SQL query against the connection.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  string  $connection
	 * @return mixed
	 */
	public static function query($sql, $bindings = array(), $connection = null)
	{
		$query = static::connection($connection)->prepare($sql);

		$result = $query->execute($bindings);

		// ---------------------------------------------------
		// For SELECT statements, the results will be returned
		// as an array of stdClasses.
		//
		// For UPDATE and DELETE statements, the number of
		// rows affected by the query will be returned.
		//
		// For all other statements, return a boolean.
		// ---------------------------------------------------
		if (strpos(Str::upper($sql), 'SELECT') === 0)
		{
			return $query->fetchAll(\PDO::FETCH_CLASS, 'stdClass');
		}
		elseif (strpos(Str::upper($sql), 'UPDATE') === 0 or strpos(Str::upper($sql), 'DELETE') === 0)
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

}