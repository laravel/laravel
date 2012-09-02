<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;

class Postgres extends Grammar {

	/**
	 * Compile a SQL INSERT and get ID statement from a Query instance.
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

	/**
	 * Returns the SQL to get the name and type of each column.
	 *
	 * @param  Query   $query
	 * @param  bool    $all_info If false, only the name & type will be returned.
	 * @return string
	 */
	public function columns(Query $query, $all_info = false)
	{
		$sql = 'SELECT ';
		if ( $all_info )
		{
			$sql .= ' *, ';
		}
		$sql .= "column_name AS name, data_type AS type FROM information_schema.columns WHERE table_name ='{$query->from}'";

		return $sql;
	}

}