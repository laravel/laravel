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
		// ---------------------------------------------------
		// Establish a SQLite PDO connection.
		// ---------------------------------------------------
		if ($config->driver == 'sqlite')
		{
			return new \PDO('sqlite:'.APP_PATH.'db/'.$config->database.'.sqlite', null, null, static::$options);
		}
		// ---------------------------------------------------
		// Establish a MySQL or Postgres PDO connection.
		// ---------------------------------------------------
		elseif ($config->driver == 'mysql' or $config->driver == 'pgsql')
		{
			$connection = new \PDO($config->driver.':host='.$config->host.';dbname='.$config->database, $config->username, $config->password, static::$options);

			// ---------------------------------------------------
			// Set the correct character set.
			// ---------------------------------------------------
			if (isset($config->charset))
			{
				$connection->prepare("SET NAMES '".$config->charset."'")->execute();
			}

			return $connection;
		}
		// ---------------------------------------------------
		// If the driver isn't supported, bail out.
		// ---------------------------------------------------
		else
		{
			throw new \Exception('Database driver '.$config->driver.' is not supported.');
		}		
	}

}