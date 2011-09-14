<?php namespace Laravel\Database\Grammars;

class MySQL extends Grammar {

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * @return string
	 */
	public function wrapper() { return '`'; }

}