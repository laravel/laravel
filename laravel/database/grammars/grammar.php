<?php namespace Laravel\Database\Grammars; use Laravel\Arr, Laravel\Database\Query;

class Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '"';

	/**
	 * All of the query componenets in the order they should be built.
	 *
	 * @var array
	 */
	protected $components = array('aggregate', 'selects', 'from', 'joins', 'wheres', 'orderings', 'limit', 'offset');

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
	final public function select(Query $query)
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
		return (($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ').$this->columnize($query->selects);
	}

	/**
	 * Compile an aggregating SELECT clause for a query.
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
		// Since creating a JOIN clause using string concatenation is a little cumbersome,
		// we will create a format we can pass to "sprintf" to make things cleaner.
		$format = '%s JOIN %s ON %s %s %s';

		foreach ($query->joins as $join)
		{
			extract($join, EXTR_SKIP);

			list($column1, $column2) = array($this->wrap($column1), $this->wrap($column2));

			$sql[] = sprintf($format, $type, $this->wrap($table), $column1, $operator, $column2);
		}

		return implode(' ', $sql);
	}

	/**
	 * Compile the WHERE clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	final protected function wheres(Query $query)
	{
		// Each WHERE clause array has a "type" that is assigned by the query builder, and
		// each type has its own compiler function. For example, "where in" queries are
		// compiled by the "where_in" function.
		//
		// The only exception to this rule are "raw" where clauses, which are simply
		// appended to the query as-is, without any further compiling.
		foreach ($query->wheres as $where)
		{
			$sql[] = ($where['type'] == 'raw') ? $where['sql'] : $where['connector'].' '.$this->{$where['type']}($where);
		}

		if (isset($sql)) return implode(' ', array_merge(array('WHERE 1 = 1'), $sql));
	}

	/**
	 * Compile a simple WHERE clause.
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
	 * Note: This method handles the compilation of single row inserts and batch inserts.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function insert(Query $query, $values)
	{
		// Force every insert to be treated like a batch insert. This simply makes creating
		// the SQL syntax a little easier on us since we can always treat the values as if
		// is an array containing multiple inserts.
		if ( ! is_array(reset($values))) $values = array($values);

		// Since we only care about the column names, we can pass any of the insert arrays
		// into the "columnize" method. The names should be the same for every insert.
		$columns = $this->columnize(array_keys(reset($values)));

		// We need to create a string of comma-delimited insert segments. Each segment contains
		// PDO place-holders for each value being inserted into the table. So, if we are inserting
		// into three columns, the string should look like this:
		//
		// (?, ?, ?), (?, ?, ?), (?, ?, ?)
		$parameters = implode(', ', array_fill(0, count($values), '('.$this->parameterize(reset($values)).')'));

		return 'INSERT INTO '.$this->wrap($query->from).' ('.$columns.') VALUES '.$parameters;
	}

	/**
	 * Compile a SQL UPDATE statment from a Query instance.
	 *
	 * Note: Since UPDATE statements can be limited by a WHERE clause, this method will
	 *       use the same WHERE clause compilation functions as the "select" method.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function update(Query $query, $values)
	{
		$columns = $this->columnize(array_keys($values), ' = ?');

		return trim('UPDATE '.$this->wrap($query->from).' SET '.$columns.' '.$this->wheres($query));
	}

	/**
	 * Compile a SQL DELETE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		return trim('DELETE FROM '.$this->wrap($query->from).' '.$this->wheres($query));
	}

	/**
	 * The following functions primarily serve as utility functions for the grammar.
	 * They perform tasks such as wrapping values in keyword identifiers or creating
	 * variable lists of bindings. Most likely, they will not need to change across
	 * various database systems.
	 */

	/**
	 * Create a comma-delimited list of wrapped column names.
	 *
	 * Optionally, an "append" value may be passed to the method. This value will be
	 * appended to every wrapped column name.
	 *
	 * @param  array   $columns
	 * @param  string  $append
	 * @return string
	 */
	protected function columnize($columns, $append = '')
	{
		foreach ($columns as $column)
		{
			$sql[] = $this->wrap($column).$append;
		}

		return implode(', ', $sql);
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * They keyword identifier used by the method is specified as a property on
	 * the grammar class so it can be conveniently overriden without changing
	 * the wrapping logic itself.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrap($value)
	{
		if (strpos(strtolower($value), ' as ') !== false) return $this->alias($value);

		foreach (explode('.', $value) as $segment)
		{
			$wrapped[] = ($segment !== '*') ? $this->wrapper.$segment.$this->wrapper : $segment;
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap an alias in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function alias($value)
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
	protected function parameterize($values)
	{
		return implode(', ', array_fill(0, count($values), '?'));
	}

}