<?php namespace Laravel\Database\Connector;

use PDO;

class SQLite extends Connector {

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $this->options);
		}
		elseif (file_exists($path = DATABASE_PATH.$config['database'].'.sqlite'))
		{
			return new PDO('sqlite:'.$path, null, null, $this->options);
		}
		elseif (file_exists($config['database']))
		{
			return new PDO('sqlite:'.$config['database'], null, null, $this->options);
		}

		throw new \Exception("SQLite database [".$config['database']."] could not be found.");
	}

}