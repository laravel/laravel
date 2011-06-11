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
		// -----------------------------------------------------
		// Connect to SQLite.
		// -----------------------------------------------------
		if ($config->driver == 'sqlite')
		{
			// -----------------------------------------------------
			// Check the application/db directory first.
			// -----------------------------------------------------
			if (file_exists($path = APP_PATH.'db/'.$config->database.'.sqlite'))
			{
				return new \PDO('sqlite:'.$path, null, null, static::$options);
			}
			// -----------------------------------------------------
			// Is the database name the full path?
			// -----------------------------------------------------
			elseif (file_exists($config->database))
			{
				return new \PDO('sqlite:'.$config->database, null, null, static::$options);
			}
		}
		// -----------------------------------------------------
		// Connect to MySQL or Postgres.
		// -----------------------------------------------------
		elseif ($config->driver == 'mysql' or $config->driver == 'pgsql')
		{
			$connection = new \PDO($config->driver.':host='.$config->host.';dbname='.$config->database, $config->username, $config->password, static::$options);

			if (isset($config->charset))
			{
				$connection->prepare("SET NAMES '".$config->charset."'")->execute();
			}

			return $connection;
		}

		throw new \Exception('Database driver '.$config->driver.' is not supported.');
	}

}