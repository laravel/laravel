<?php namespace Laravel\Database;

use PDO;

abstract class Connector {

	/**
	 * The PDO connection options.
	 *
	 * @var array
	 */
	public $options = array(
		PDO::ATTR_CASE              => PDO::CASE_LOWER,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false,
	);

	/**
	 * Establish a PDO database connection.
	 *
	 * @param  array  $config
	 * @return PDO
	 */
	abstract public function connect($config);

}