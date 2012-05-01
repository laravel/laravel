<?php namespace Laravel;

use Laravel\Database\Expression;
use Laravel\Database\Connection;

class Database {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	public static $connections = array();

	/**
	 * The third-party driver registrar.
	 *
	 * @var array
	 */
	public static $registrar = array();

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
	 * @return Database\Connection
	 */
	public static function connection($connection = null)
	{
		if (is_null($connection)) $connection = Config::get('database.default');

		if ( ! isset(static::$connections[$connection]))
		{
			$config = Config::get("database.connections.{$connection}");

			if (is_null($config))
			{
				throw new \Exception("Database connection is not defined for [$connection].");
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
		return static::connector($config['driver'])->connect($config);
	}

	/**
	 * Create a new database connector instance.
	 *
	 * @param  string     $driver
	 * @return Database\Connectors\Connector
	 */
	protected static function connector($driver)
	{
		if (isset(static::$registrar[$driver]))
		{
			return static::$registrar[$driver]['connector']();
		}

		switch ($driver)
		{
			case 'sqlite':
				return new Database\Connectors\SQLite;

			case 'mysql':
				return new Database\Connectors\MySQL;

			case 'pgsql':
				return new Database\Connectors\Postgres;

			case 'sqlsrv':
				return new Database\Connectors\SQLServer;

			default:
				throw new \Exception("Database driver [$driver] is not supported.");
		}
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * @param  string          $table
	 * @param  string          $connection
	 * @return Database\Query
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
	 * Get the profiling data for all queries.
	 *
	 * @return array
	 */
	public static function profile()
	{
		return Database\Connection::$queries;
	}

	/**
	 * Register a database connector and grammars.
	 *
	 * @param  string   $name
	 * @param  Closure  $connector
	 * @param  Closure  $query
	 * @param  Closure  $schema
	 * @return void
	 */
	public static function register($name, Closure $connector, $query = null, $schema = null)
	{
		if (is_null($query)) $query = '\Laravel\Database\Query\Grammars\Grammar';

		static::$registrar[$name] = compact('connector', 'query', 'schema');
	}

	/**
	 * Magic Method for calling methods on the default database connection.
	 *
	 * <code>
	 *		// Get the driver name for the default database connection
	 *		$driver = DB::driver();
	 *
	 *		// Execute a fluent query on the default database connection
	 *		$users = DB::table('users')->get();
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::connection(), $method), $parameters);
	}

}