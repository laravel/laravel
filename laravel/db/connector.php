<?php namespace Laravel\DB;

use Laravel\Config;

class Connector {

	/**
	 * The PDO connection options.
	 *
	 * @var array
	 */
	public $options = array(
			\PDO::ATTR_CASE => \PDO::CASE_LOWER,
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
			\PDO::ATTR_STRINGIFY_FETCHES => false,
			\PDO::ATTR_EMULATE_PREPARES => false,
	);

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  object  $connection
	 * @return PDO
	 */
	public function connect($config)
	{
		switch ($config->driver)
		{
			case 'sqlite':
				return $this->connect_to_sqlite($config);

			case 'mysql':
			case 'pgsql':
				return $this->connect_to_server($config);
			
			default:
				return $this->connect_to_generic($config);
		}

		throw new \Exception('Database driver '.$config->driver.' is not supported.');
	}

	/**
	 * Establish a PDO connection to a SQLite database.
	 *
	 * SQLite database paths can be specified either relative to the application/db
	 * directory, or as an absolute path to any location on the file system. In-memory
	 * databases are also supported.
	 *
	 * @param  object  $config
	 * @return PDO
	 */
	private function connect_to_sqlite($config)
	{
		if ($config->database == ':memory:')
		{
			return new \PDO('sqlite::memory:', null, null, $this->options);
		}
		elseif (file_exists($path = DATABASE_PATH.$config->database.'.sqlite'))
		{
			return new \PDO('sqlite:'.$path, null, null, $this->options);
		}
		elseif (file_exists($config->database))
		{
			return new \PDO('sqlite:'.$config->database, null, null, $this->options);
		}

		throw new \Exception("SQLite database [".$config->database."] could not be found.");
	}

	/**
	 * Connect to a MySQL or PostgreSQL database server.
	 *
	 * @param  object  $config
	 * @return PDO
	 */
	private function connect_to_server($config)
	{
		$dsn = $config->driver.':host='.$config->host.';dbname='.$config->database;

		if (isset($config->port))
		{
			$dsn .= ';port='.$config->port;
		}

		$connection = new \PDO($dsn, $config->username, $config->password, $this->options);

		if (isset($config->charset))
		{
			$connection->prepare("SET NAMES '".$config->charset."'")->execute();
		}

		return $connection;
	}

	/**
	 * Connect to a generic data source.
	 *
	 * @param  object  $config
	 * @return PDO
	 */
	private function connect_to_generic($config)
	{
		return new \PDO($config->driver.':'.$config->dsn, $config->username, $config->password, $this->options);
	}

}