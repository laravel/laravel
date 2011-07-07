<?php namespace System\DB;

class Connector {

	/**
	 * The PDO connection options.
	 *
	 * @var array
	 */
	public static $options = array(
			\PDO::ATTR_CASE => \PDO::CASE_LOWER,
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
			\PDO::ATTR_STRINGIFY_FETCHES => false,
	);

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  object  $config
	 * @return PDO
	 */
	public static function connect($config)
	{
		if ($config->driver == 'sqlite')
		{
			return static::connect_to_sqlite($config);
		}
		elseif ($config->driver == 'mysql' or $config->driver == 'pgsql')
		{
			return static::connect_to_server($config);
		}

		throw new \Exception('Database driver '.$config->driver.' is not supported.');
	}

	/**
	 * Establish a PDO connection to a SQLite database.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	private static function connect_to_sqlite($config)
	{
		// Database paths can either be specified relative to the application/storage/db
		// directory, or as an absolute path.

		if (file_exists($path = APP_PATH.'storage/db/'.$config->database.'.sqlite'))
		{
			return new \PDO('sqlite:'.$path, null, null, static::$options);
		}
		elseif (file_exists($config->database))
		{
			return new \PDO('sqlite:'.$config->database, null, null, static::$options);
		}
		else
		{
			throw new \Exception("SQLite database [".$config->database."] could not be found.");
		}
	}

	/**
	 * Connect to a MySQL or PostgreSQL database server.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	private static function connect_to_server($config)
	{
		$dsn = $config->driver.':host='.$config->host.';dbname='.$config->database;

		if (isset($config->port))
		{
			$dsn .= ';port='.$config->port;
		}

		$connection = new \PDO($dsn, $config->username, $config->password, static::$options);

		if (isset($config->charset))
		{
			$connection->prepare("SET NAMES '".$config->charset."'")->execute();
		}

		return $connection;
	}

}