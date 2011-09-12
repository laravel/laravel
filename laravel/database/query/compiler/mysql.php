<?php namespace Laravel\Database\Query\Compiler;

class MySQL extends Compiler {

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * @return string
	 */
	public function wrapper() { return '`'; }

}