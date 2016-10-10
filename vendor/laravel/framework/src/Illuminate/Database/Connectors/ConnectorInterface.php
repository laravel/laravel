<?php namespace Illuminate\Database\Connectors;

interface ConnectorInterface {

	/**
	 * Establish a database connection.
	 *
	 * @param  array  $config
	 * @return \PDO
	 */
	public function connect(array $config);

}
