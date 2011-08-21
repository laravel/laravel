<?php namespace Laravel\DB;

class Compiler {

	/**
	 * Compile a SQL SELECT statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function select(Query $query)
	{
		$sql = $query->select.' '.$query->from.' '.$query->where;

		if ( ! is_null($query->order))
		{
			$sql .= ' '.$query->order;
		}

		if ( ! is_null($query->limit))
		{
			$sql .= ' '.$query->limit;
		}

		if ( ! is_null($query->offset))
		{
			$sql .= ' '.$query->offset;
		}

		return $sql;
	}

	/**
	 * Compile a SQL INSERT statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function insert(Query $query, $values)
	{
		$sql = 'INSERT INTO '.$query->wrap($query->table);

		$columns = array_map(array($query, 'wrap'), array_keys($values));

		return $sql .= ' ('.implode(', ', $columns).') VALUES ('.$query->parameterize($values).')';
	}

	/**
	 * Compile a SQL UPDATE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function update(Query $query, $values)
	{
		$sql = 'UPDATE '.$query->wrap($query->table).' SET ';

		foreach (array_keys($values) as $column)
		{
			$sets[] = $query->wrap($column).' = ?';
		}
		
		return $sql.implode(', ', $sets).' '.$query->where;
	}

	/**
	 * Compile a SQL DELETE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		return 'DELETE FROM '.$query->wrap($query->table).' '.$query->where;
	}

}