<?php namespace System\DB\Query;

class Compiler {

	/**
	 * Build a SQL SELECT statement.
	 *
	 * @param  Query  $query
	 * @return string
	 */
	public static function select($query)
	{
		$sql = $query->select.' '.$query->from.' '.$query->where;

		if (count($query->orderings) > 0)
		{
			$sql .= ' ORDER BY '.implode(', ', $query->orderings);
		}

		if ( ! is_null($query->limit))
		{
			$sql .= ' LIMIT '.$query->limit;
		}

		if ( ! is_null($query->offset))
		{
			$sql .= ' OFFSET '.$query->offset;
		}

		return $sql;
	}

	/**
	 * Build a SQL INSERT statement.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public static function insert($query, $values)
	{
		$sql = 'INSERT INTO '.$query->table.' (';

		// ---------------------------------------------------
		// Wrap each column name in keyword identifiers.
		// ---------------------------------------------------
		$columns = array();

		foreach (array_keys($values) as $column)
		{
			$columns[] = $query->wrap($column);
		}

		return $sql .= implode(', ', $columns).') VALUES ('.$query->parameterize($values).')';
	}

	/**
	 * Build a SQL UPDATE statement.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public static function update($query, $values)
	{
		$sql = 'UPDATE '.$query->table.' SET ';

		// ---------------------------------------------------
		// Add each column set the query.
		// ---------------------------------------------------
		$columns = array();

		foreach (array_keys($values) as $column)
		{
			$columns[] = $query->wrap($column).' = ?';
		}

		return $sql .= implode(', ', $columns).' '.$query->where;		
	}

	/**
	 * Build a SQL DELETE statement.
	 *
	 * @param  Query  $query
	 * @return string
	 */
	public static function delete($query)
	{
		return 'DELETE FROM '.$query->wrap($query->table).' '.$query->where;
	}

}