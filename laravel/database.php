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
	 * The registered database driver models.
	 *
	 * @var array
	 */
	public static $drivers = array(
		'sqlite' => array(
			'connector' => 'Laravel\\Database\\Connectors\\SQLite',
			'schema' => 'Laravel\\Database\\Schema\\Grammars\\SQLite',
		),
		'mysql' => array(
			'connector' => 'Laravel\\Database\\Connectors\\MySQL',
			'schema' => 'Laravel\\Database\\Schema\\Grammars\\MySQL',
			'query' => 'Laravel\\Database\\Query\\Grammars\\MySQL',
		),
		'pgsql' => array(
			'connector' => 'Laravel\\Database\\Connectors\\Postgres',
			'schema' => 'Laravel\\Database\\Schema\\Grammars\\Postgres',
		),
		'sqlsrv' => array(
			'connector' => 'Laravel\\Database\\Connectors\\SQLServer',
			'schema' => 'Laravel\\Database\\Schema\\Grammars\\SQLServer',
			'query' => 'Laravel\\Database\\Query\\Grammars\\SQLServer',
		),
	);

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
	 * Register an external database driver's models
	 *
	 * @param	mixed 	$name  	 name of driver to register, or an array of name=>model pairs
	 * @param	mixed	$model   name of connector model or array of models by type
	 */
	public static function register($name,$model=null) 
	{
		if ( ! is_array($name)) $name = array( $name => $model );
	
		foreach ($name as $driver => $model)
		{
			if (isset(static::$drivers[$driver])) throw new \Exception("Database driver [$driver] is already registered.");
			if ( ! is_array($model)) $model = array('connector'=>$model);
			
			if ( ! isset($model['connector'])) throw new \Exception("Registration of [$driver] provides no connector class");
			
			$tests = array(
						'connector' => 'Laravel\\Database\\Connectors\\Connector',
						'schema' => 'Laravel\\Database\\Schema\\Grammars\\Grammar',
						'query' => 'Laravel\\Database\\Query\\Grammars\\Grammar',
					 );
			
			foreach ($tests as $test => $testclass) {
				if ( ! isset($model[$test])) continue;
				if ( ! class_exists($model[$test])) throw new \Exception(ucfirst($test)." class [{$model[$test]}] is not defined.");
				if ( ! is_subclass_of($model[$test],$testclass)) throw new \Exception(ucfirst($test)." class [{$model[$test]}] does not extend [$testclass].");
			}
			
			static::$drivers[$driver] = $model;
		}		
	}

	/**
	 * Create a new database connector instance.
	 *
	 * @param  string     $driver
	 * @return Database\Connectors\Connector
	 */
	protected static function connector($driver)
	{
		if (isset(static::$drivers[$driver]['connector']))
		{
			return new static::$drivers[$driver]['connector'];
		}
		throw new \Exception("Database driver [$driver] is not supported.");
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