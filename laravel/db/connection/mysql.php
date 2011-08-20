<?php namespace Laravel\DB\Connection;

use Laravel\DB\Connection;

class MySQL extends Connection {

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * MySQL uses a non-standard wrapper
	 *
	 * @return string
	 */
	public function wrapper() { return '`'; }

}