<?php namespace Laravel\DB\Query;

use Laravel\DB\Query;

class MySQL extends Query {

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * MySQL uses a non-standard wrapper
	 *
	 * @return string
	 */
	public function wrapper()
	{
		return '`';
	}

}