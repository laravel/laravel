<?php namespace Laravel\Database\Grammars; use Laravel\Arr, Laravel\Database\Query;

class Grammar {

	/**
	 * All of the query componenets in the order they should be built.
	 *
	 * @var array
	 */
	protected $components = array('selects', 'from', 'joins', 'wheres', 'orderings', 'limit', 'offset');

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '"';

	/**
	 * Compile a SQL SELECT statement from a Query instance.
	 *
	 * The query will be compiled according to the order of the elements specified
	 * in the "components" property. The entire query is pased into each component
	 * compiler for convenience.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function select(Query $query)
	{
		$sql = array();

		// Iterate through each query component, calling the compiler for that
		// component, and passing the query instance into the compiler.
		foreach ($this->components as $component)
		{
			if ( ! is_null($query->$component)) $sql[] = call_user_func(array($this, $component), $query);
		}

		return implode(' ', Arr::without($sql, array(null, '')));
	}

	/**
	 * Compile the SELECT clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function selects(Query $query)
	{
		if ( ! is_null($query->aggregate)) return $this->aggregate($query);

		return (($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ').$this->columnize($query->selects);
	}

	/**
	 * Compile an aggregating SELECT clause for a query.
	 *
	 * This method compiled the SELECT clauses for queries built using the
	 * count, max, min, abs, and sum methods on the fluent query builder.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function aggregate(Query $query)
	{
		list($aggregator, $column) = array($query->aggregate['aggregator'], $query->aggregate['column']);

		return 'SELECT '.$aggregator.'('.$this->wrap($column).')';
	}

	/**
	 * Compile the FROM clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function from(Query $query)
	{
		return 'FROM '.$this->wrap($query->from);
	}

	/**
	 * Compile the JOIN clauses for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function joins(Query $query)
	{
		foreach ($query->joins as $join)
		{
			extract($join);

			$sql[] = $type.' JOIN '.$this->wrap($table).' ON '.$this->wrap($column1).' '.$operator.' '.$this->wrap($column2);
		}

		return implode(' ', $sql);
	}

	/**
	 * Compile the WHERE clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function wheres(Query $query)
	{
		// Each WHERE clause array has a "type" that is assigned by the query builder, and
		// each type has its own compiler function. For example, "where in" queries are
		// compiled by the "where_in" function.
		//
		// The only exception to this rule are "raw" where clauses, which are simply
		// appended to the query as-is, without any further compiling.
		foreach ($wheres as $where)
		{
			$sql[] = ($where['type'] == 'raw') ? $where['sql'] : $where['connector'].' '.$this->{$where['type']}($where);
		}

		return implode(' ', array_merge(array('WHERE 1 = 1'), $sql));
	}

	/**
	 * Compile a simple WHERE clause.
	 *
	 * This method compiles the SQL for the "where" and "or_where" query functions.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where($where)
	{
		return $this->wrap($where['column']).' '.$where['operator'].' ?';
	}

	/**
	 * Compile a WHERE IN clause.
	 *
	 * This method compiled the SQL for all of the "where_in" style query functions.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_in($where)
	{
		$operator = ($where['not']) ? 'NOT IN' : 'IN';

		return $this->wrap($where['column']).' '.$operator.' ('.$this->parameterize($where['values']).')';
	}

	/**
	 * Compile a WHERE NULL clause.
	 *
	 * This method compiles the SQL for all of the "where_null" style query functions.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_null($where)
	{
		$operator = ($where['not']) ? 'NOT NULL' : 'NULL';

		return $this->wrap($where['column']).' IS '.$operator;
	}

	/**
	 * Compile ORDER BY clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function orderings(Query $query)
	{
		foreach ($query->orderings as $ordering)
		{
			$sql[] = $this->wrap($ordering['column']).' '.strtoupper($ordering['direction']);
		}

		return 'ORDER BY '.implode(', ', $sql);
	}

	/**
	 * Compile the LIMIT clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function limit(Query $query)
	{
		return 'LIMIT '.$query->limit;
	}

	/**
	 * Compile the OFFSET clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function offset(Query $query)
	{
		return 'OFFSET '.$query->offset;
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
		$columns = implode(', ', $this->columnize(array_keys($values)));

		return 'INSERT INTO '.$this->wrap($query->from).' ('.$columns.') VALUES ('.$this->parameterize($values).')';
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
		foreach (array_keys($values) as $column)
		{
			$sets[] = $this->wrap($column).' = ?';
		}

		$sql = 'UPDATE '.$this->wrap($query->from).' SET '.implode(', ', $sets);

		return (count($query->wheres) > 0) ? $sql.' '.$this->wheres($query->wheres) : $sql;
	}

	/**
	 * Compile a SQL DELETE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		$sql = 'DELETE FROM '.$this->wrap($query->from);

		return (count($query->wheres) > 0) ? $sql.' '.$this->wheres($query->wheres) : $sql;
	}

	/**
	 * Create a comma-delimited list of wrapped column names.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	protected function columnize($columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false) return $this->wrap_alias($value);

		foreach (explode('.', $value) as $segment)
		{
			$wrapped[] = ($segment != '*') ? $this->wrapper.$segment.$this->wrapper : $segment;
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap an alias in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap_alias($value)
	{
		$segments = explode(' ', $value);

		return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
	}

	/**
	 * Create query parameters from an array of values.
	 *
	 * @param  array   $values
	 * @return string
	 */
	public function parameterize($values)
	{
		return implode(', ', array_fill(0, count($values), '?'));
	}

}