<?php namespace Illuminate\Database\Connectors;

use PDO;

class Connector {

	/**
	 * The default PDO connection options.
	 *
	 * @var array
	 */
	protected $options = array(
			PDO::ATTR_CASE => PDO::CASE_NATURAL,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			PDO::ATTR_STRINGIFY_FETCHES => false,
			PDO::ATTR_EMULATE_PREPARES => false,
	);

	/**
	 * Get the PDO options based on the configuration.
	 *
	 * @param  array  $config
	 * @return array
	 */
	public function getOptions(array $config)
	{
		$options = array_get($config, 'options', array());

		return array_diff_key($this->options, $options) + $options;
	}

	/**
	 * Create a new PDO connection.
	 *
	 * @param  string  $dsn
	 * @param  array   $config
	 * @param  array   $options
	 * @return PDO
	 */
	public function createConnection($dsn, array $config, array $options)
	{
		$username = array_get($config, 'username');

		$password = array_get($config, 'password');

		return new PDO($dsn, $username, $password, $options);
	}

	/**
	 * Get the default PDO connection options.
	 *
	 * @return array
	 */
	public function getDefaultOptions()
	{
		return $this->options;
	}

	/**
	 * Set the default PDO connection options.
	 *
	 * @param  array  $options
	 * @return void
	 */
	public function setDefaultOptions(array $options)
	{
		$this->options = $options;
	}

}
