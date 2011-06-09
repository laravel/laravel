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
		// ---------------------------------------------------
		// Add the SELECT, FROM, and WHERE clauses.
		// ---------------------------------------------------
		$sql = $query->select.' '.$query->from.' '.$query->where;

		// ---------------------------------------------------
		// Add the ORDER BY clause.
		// ---------------------------------------------------
		if (count($query->orderings) > 0)
		{
			$sql .= ' ORDER BY '.implode(', ', $query->orderings);
		}

		// ---------------------------------------------------
		// Add the LIMIT.
		// ---------------------------------------------------
		if ( ! is_null($query->limit))
		{
			$sql .= ' LIMIT '.$query->limit;
		}

		// ---------------------------------------------------
		// Add the OFFSET.
		// ---------------------------------------------------
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
		// ---------------------------------------------------
		// Start the query. Add the table name.
		// ---------------------------------------------------
		$sql = 'INSERT INTO '.$query->table.' (';

		// ---------------------------------------------------
		// Wrap each column name in keyword identifiers.
		// ---------------------------------------------------
		$columns = array();

		foreach (array_keys($values) as $column)
		{
			$columns[] = $query->wrap($column);
		}

		// ---------------------------------------------------
		// Concatenate the column names and values.
		// ---------------------------------------------------
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
		// ---------------------------------------------------
		// Start the query. Add the table name.
		// ---------------------------------------------------
		$sql = 'UPDATE '.$query->table.' SET ';

		// ---------------------------------------------------
		// Wrap each column name in keyword identifiers.
		// ---------------------------------------------------
		$columns = array();

		foreach (array_keys($values) as $column)
		{
			$columns[] = $query->wrap($column).' = ?';
		}

		// ---------------------------------------------------
		// Concatenate the column names and the WHERE clause.
		// ---------------------------------------------------
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