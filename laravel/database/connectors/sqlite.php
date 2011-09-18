<?php namespace Laravel\Database\Connectors; use PDO;

class SQLite extends Connector {

	/**
	 * Establish a PDO database connection for a given database configuration.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	public function connect($config)
	{
		$options = $this->options($config);

		if ($config['database'] == ':memory:')
		{
			return new PDO('sqlite::memory:', null, null, $options);
		}
		elseif (file_exists($path = DATABASE_PATH.$config['database'].'.sqlite'))
		{
			return new PDO('sqlite:'.$path, null, null, $options);
		}
		elseif (file_exists($config['database']))
		{
			return new PDO('sqlite:'.$config['database'], null, null, $options);
		}

		throw new \Exception("SQLite database [{$config['database']}] could not be found.");
	}

}