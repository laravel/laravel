<?php namespace Laravel\Database\Grammars;

use Laravel\Database\Query;

class Grammar {

	/**
	 * Compile a SQL SELECT statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function select(Query $query)
	{
		if ( ! is_null($query->aggregate))
		{
			$sql[] = $this->compile_aggregate($query->aggregate['aggregator'], $query->aggregate['column']);
		}
		else
		{
			$sql[] = $this->compile_select($query);
		}

		$sql[] = $this->compile_from($query->table);

		foreach (array('joins', 'wheres', 'orderings', 'limit', 'offset') as $clause)
		{
			if ( ! is_null($query->$clause)) $sql[] = call_user_func(array($this, 'compile_'.$clause), $query->$clause);
		}

		return implode(' ', array_filter($sql, function($value) { return ! is_null($value) and (string) $value !== ''; }));
	}

	/**
	 * Compile the query SELECT clause.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function compile_select(Query $query)
	{
		return (($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ').implode(', ', array_map(array($this, 'wrap'), $query->select));
	}

	/**
	 * Compile the query SELECT clause with an aggregate function.
	 *
	 * @param  string  $aggregator
	 * @param  string  $column
	 * @return string
	 */
	protected function compile_aggregate($aggregator, $column)
	{
		return 'SELECT '.$aggregator.'('.$this->wrap($column).') AS '.$this->wrap('aggregate');
	}

	/**
	 * Compile the query FROM clause.
	 *
	 * Note: This method does not compile any JOIN clauses. Joins are compiled by the compile_joins method.
	 *
	 * @param  string  $table
	 * @return string
	 */
	protected function compile_from($table)
	{
		return 'FROM '.$this->wrap($table);
	}

	/**
	 * Compile the query JOIN clauses.
	 *
	 * @param  array   $joins
	 * @return string
	 */
	protected function compile_joins($joins)
	{
		foreach ($joins as $join)
		{
			extract($join);

			$sql[] = $type.' JOIN '.$this->wrap($table).' ON '.$this->wrap($column1).' '.$operator.' '.$this->wrap($column2);
		}

		return implode(' ', $sql);
	}

	/**
	 * Compile the query WHERE clauses.
	 *
	 * @param  array   $wheres
	 * @return string
	 */
	protected function compile_wheres($wheres)
	{
		$sql = array('WHERE 1 = 1');

		foreach ($wheres as $where)
		{
			$sql[] = (is_string($where)) ? $where : $where['connector'].' '.$this->{'compile_'.$where['type']}($where);
		}

		return implode(' ', $sql);
	}

	/**
	 * Compile a simple WHERE clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function compile_where($where)
	{
		return $this->wrap($where['column']).' '.$where['operator'].' ?';
	}

	/**
	 * Compile a WHERE IN clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function compile_where_in($where)
	{
		$operator = ($where['not']) ? 'NOT IN' : 'IN';

		return $this->wrap($where['column']).' '.$operator.' ('.$this->parameterize($where['values']).')';
	}

	/**
	 * Compile a WHERE NULL clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function compile_where_null($where)
	{
		$operator = ($where['not']) ? 'NOT NULL' : 'NULL';

		return $this->wrap($where['column']).' IS '.$operator;
	}

	/**
	 * Compile the query ORDER BY clause.
	 *
	 * @param  array   $orderings
	 * @return string
	 */
	protected function compile_orderings($orderings)
	{
		foreach ($orderings as $ordering)
		{
			$sql[] = $this->wrap($ordering['column']).' '.strtoupper($ordering['direction']);
		}

		return 'ORDER BY '.implode(', ', $sql);
	}

	/**
	 * Compile the query LIMIT.
	 *
	 * @param  int     $limit
	 * @return string
	 */
	protected function compile_limit($limit)
	{
		return 'LIMIT '.$limit;
	}

	/**
	 * Compile the query OFFSET.
	 *
	 * @param  int     $offset
	 * @return string
	 */
	protected function compile_offset($offset)
	{
		return 'OFFSET '.$offset;
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
		$columns = array_map(array($this, 'wrap'), array_keys($values));

		return 'INSERT INTO '.$this->wrap($query->table).' ('.implode(', ', $columns).') VALUES ('.$this->parameterize($values).')';
	}

	/**
	 * Compile a SQL INSERT statment that returns an auto-incrementing ID from a Query instance.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function insert_get_id(Query $query, $values)
	{
		return $this->insert($query, $values);
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
		foreach (array_keys($values) as $column) { $sets[] = $this->wrap($column).' = ?'; }

		$sql = 'UPDATE '.$this->wrap($query->table).' SET '.implode(', ', $sets);

		return (count($query->wheres) > 0) ? $sql.' '.$this->compile_wheres($query->wheres) : $sql;
	}

	/**
	 * Compile a SQL DELETE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		$sql = 'DELETE FROM '.$this->wrap($query->table);

		return (count($query->wheres) > 0) ? $sql.' '.$this->compile_wheres($query->wheres) : $sql;
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string      $value
	 * @return string
	 */
	public function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false) return $this->wrap_alias($value);

		foreach (explode('.', $value) as $segment)
		{
			$wrapped[] = ($segment != '*') ? $this->wrapper().$segment.$this->wrapper() : $segment;
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap an alias in keyword identifiers.
	 *
	 * @param  string      $value
	 * @return string
	 */
	public function wrap_alias($value)
	{
		$segments = explode(' ', $value);

		return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
	}

	/**
	 * Get the keyword identifier wrapper for the connection.
	 *
	 * @return string
	 */
	public function wrapper() { return '"'; }

	/**
	 * Create query parameters from an array of values.
	 *
	 * @param  array  $values
	 * @return string
	 */
	public function parameterize($values) { return implode(', ', array_fill(0, count($values), '?')); }

}