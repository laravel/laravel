<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;

class Postgres extends Grammar {

	/**
	 * Compile a SQL INSERT and get ID statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @param  string  $column
	 * @return string
	 */
	public function insert_get_id(Query $query, $values, $column)
	{
		return $this->insert($query, $values)." RETURNING $column";
	}

}