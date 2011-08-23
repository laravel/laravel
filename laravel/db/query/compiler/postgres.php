<?php namespace Laravel\DB\Query\Compiler;

use Laravel\DB\Query\Compiler;

class Postgres extends Compiler {

	/**
	 * Compile a SQL INSERT statment that returns an auto-incrementing ID from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function insert_get_id(Query $query, $values)
	{
		return $this->insert($query, $values).' RETURNING '.$this->wrap('id');
	}

}