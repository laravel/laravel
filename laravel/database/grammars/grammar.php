<?php namespace Laravel\Database\Grammars;

use Laravel\Arr;
use Laravel\Database\Query;
use Laravel\Database\Expression;

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
	 * Each derived compiler may adjust these components and place them in the
	 * order needed for its particular database system, providing greater
	 * control over how the query is structured.
	 *
	 * @var array
	 */
	protected $components = array(
		'aggregate',
		'selects',
		'from',
		'joins',
		'wheres',
		'orderings',
		'limit',
		'offset'
	);

	/**
	 * Compile a SQL SELECT statement from a Query instance.
	 *
	 * The query will be compiled according to the order of the elements specified
	 * in the "components" property. The entire query is passed into each component
	 * compiler for convenience.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	final public function select(Query $query)
	{
		$sql = array();

		foreach ($this->components as $component)
		{
			if ( ! is_null($query->$component))
			{
				$sql[] = call_user_func(array($this, $component), $query);
			}
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
		$select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

		return $select.$this->columnize($query->selects);
	}

	/**
	 * Compile an aggregating SELECT clause for a query.
	 *
	 * If an aggregate function is called on the query instance, no select
	 * columns will be set, so it is safe to assume that the "selects"
	 * compiler function will not be called. We can simply build the
	 * aggregating select clause within this function.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function aggregate(Query $query)
	{
		$column = $this->wrap($query->aggregate['column']);

		return 'SELECT '.$query->aggregate['aggregator'].'('.$column.')';
	}

	/**
	 * Compile the FROM clause for a query.
	 *
	 * This method should not handle the construction of "join" clauses.
	 * The join clauses will be constructured by their own compiler.
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
		$format = '%s JOIN %s ON %s %s %s';

		foreach ($query->joins as $join)
		{
			$table = $this->wrap($join['table']);

			$column1 = $this->wrap($join['column1']);

			$column2 = $this->wrap($join['column2']);

			$sql[] = sprintf($format, $join['type'], $table, $column1, $join['operator'], $column2);
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
		// Each WHERE clause array has a "type" that is assigned by the query
		// builder, and each type has its own compiler function. We will simply
		// iterate through the where clauses and call the appropriate compiler
		// for each clause.
		foreach ($query->wheres as $where)
		{
			$sql[] = $where['connector'].' '.$this->{$where['type']}($where);
		}

		if (isset($sql)) return implode(' ', array_merge(array('WHERE 1 = 1'), $sql));
	}

	/**
	 * Compile a simple WHERE clause.
	 *
	 * This method handles the compilation of the structures created by the
	 * "where" and "or_where" methods on the query builder.
	 *
	 * This method also handles database expressions, so care must be taken
	 * to implement this functionality in any derived database grammars.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where($where)
	{
		$parameter = $this->parameter($where['value']);

		return $this->wrap($where['column']).' '.$where['operator'].' '.$parameter;
	}

	/**
	 * Compile a WHERE IN clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_in($where)
	{
		$parameters = $this->parameterize($where['values']);

		return $this->wrap($where['column']).' IN ('.$parameters.')';
	}

	/**
	 * Compile a WHERE NOT IN clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_not_in($where)
	{
		$parameters = $this->parameterize($where['values']);

		return $this->wrap($where['column']).' NOT IN ('.$parameters.')';
	}

	/**
	 * Compile a WHERE NULL clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_null($where)
	{
		return $this->wrap($where['column']).' IS NULL';
	}

	/**
	 * Compile a WHERE NULL clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_not_null($where)
	{
		return $this->wrap($where['column']).' IS NOT NULL';
	}

	/**
	 * Compile a raw WHERE clause.
	 *
	 * @param  string  $where
	 * @return string
	 */
	protected function where_raw($where)
	{
		return $where;
	}

	/**
	 * Compile the ORDER BY clause for a query.
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
		// Force every insert to be treated like a batch insert. This simple makes
		// creating the SQL syntax a little easier on us since we can always treat
		// the values as if it is an array containing multiple inserts.
		if ( ! is_array(reset($values))) $values = array($values);

		// Since we only care about the column names, we can pass any of the insert
		// arrays into the "columnize" method. The names should be the same for
		// every insert to the table.
		$columns = $this->columnize(array_keys(reset($values)));

		// Build the list of parameter place-holders of values bound to the query.
		// Each insert should have the same number of bound paramters, so we can
		// just use the first array of values.
		$parameters = $this->parameterize(reset($values));

		$parameters = implode(', ', array_fill(0, count($values), '('.$parameters.')'));

		return 'INSERT INTO '.$this->wrap($query->from).' ('.$columns.') VALUES '.$parameters;
	}

	/**
	 * Compile a SQL UPDATE statment from a Query instance.
	 *
	 * Note: Since UPDATE statements can be limited by a WHERE clause,
	 *       this method will use the same WHERE clause compilation
	 *       functions as the "select" method.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function update(Query $query, $values)
	{
		foreach ($values as $column => $value)
		{
			$columns[] = $this->wrap($column).' = '.$this->parameter($value);
		}

		$columns = implode(', ', $columns);

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
	 * The following functions primarily serve as utility functions for
	 * the grammar. They perform tasks such as wrapping values in keyword
	 * identifiers or creating variable lists of bindings.
	 */

	/**
	 * Create a comma-delimited list of wrapped column names.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	final public function columnize($columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * They keyword identifier used by the method is specified as
	 * a property on the grammar class so it can be conveniently
	 * overriden without changing the wrapping logic itself.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		// If the value being wrapped contains a column alias, we need to wrap
		// it a little differently since each segment must be wrapped and not
		// the entire string.
		if (strpos(strtolower($value), ' as ') !== false)
		{
			return $this->alias($value);
		}

		// Expressions should be injected into the query as raw strings, so we
		// do not want to wrap them in any way. We will just return the string
		// value from the expression to be included in the query.
		if ($value instanceof Expression) return $value->get();

		foreach (explode('.', $value) as $segment)
		{
			if ($segment === '*')
			{
				$wrapped[] = $segment;
			}
			else
			{
				$wrapped[] = $this->wrapper.$segment.$this->wrapper;
			}
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
	public function parameterize($values)
	{
		return implode(', ', array_map(array($this, 'parameter'), $values));
	}

	/**
	 * Get the appropriate query parameter string for a value.
	 *
	 * If the value is an expression, the raw expression string should
	 * be returned, otherwise, the parameter place-holder will be
	 * returned by the method.
	 *
	 * @param  mixed   $value
	 * @return string
	 */
	public function parameter($value)
	{
		return ($value instanceof Expression) ? $value->get() : '?';
	}

}