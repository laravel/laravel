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
		foreach (array('add_select', 'add_from', 'add_where', 'add_order', 'add_limit', 'add_offset') as $builder)
		{
			$sql[] = $this->$builder($query);
		}

		foreach ($sql as $key => $value) { if (is_null($value) or $value === '') unset($sql[$key]); }

		return implode(' ', $sql);
	}

	/**
	 * Get the SELECT clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_select(Query $query)
	{
		return $query->select;
	}

	/**
	 * Get the FROM clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_from(Query $query)
	{
		return $query->from;
	}

	/**
	 * Get the WHERE clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_where(Query $query)
	{
		return $query->where;
	}

	/**
	 * Get the ORDER BY clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_order(Query $query)
	{
		if (count($query->orderings) > 0) return 'ORDER BY'.implode(', ', $query->orderings);
	}

	/**
	 * Get the LIMIT clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_limit(Query $query)
	{
		if ( ! is_null($query->limit)) return 'LIMIT '.$query->limit;
	}

	/**
	 * Get the OFFSET clause from the Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function add_offset(Query $query)
	{
		if ( ! is_null($query->offset)) return 'OFFSET '.$query->offset;
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