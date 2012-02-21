<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;
use Laravel\Database\Expression;

class Grammar extends \Laravel\Database\Grammar {

	/**
	 * All of the query componenets in the order they should be built.
	 *
	 * @var array
	 */
	protected $components = array(
		'aggregate', 'selects', 'from', 'joins', 'wheres',
		'groupings', 'orderings', 'limit', 'offset',
	);

	/**
	 * Compile a SQL SELECT statement from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function select(Query $query)
	{
		return $this->concatenate($this->components($query));
	}

	/**
	 * Generate the SQL for every component of the query.
	 *
	 * @param  Query  $query
	 * @return array
	 */
	final protected function components($query)
	{
		// Each portion of the statement is compiled by a function corresponding
		// to an item in the components array. This lets us to keep the creation
		// of the query very granular and very flexible.
		//
		// Note that each component also connects to a public property on the
		// query instance, allowing us to pass the correct data into each
		// of the compiler functions.
		foreach ($this->components as $component)
		{
			if ( ! is_null($query->$component))
			{
				$sql[$component] = call_user_func(array($this, $component), $query);
			}
		}

		return (array) $sql;
	}

	/**
	 * Concatenate an array of SQL segments, removing those that are empty.
	 *
	 * @param  array   $components
	 * @return string
	 */
	final protected function concatenate($components)
	{
		return implode(' ', array_filter($components, function($value)
		{
			return (string) $value !== '';
		}));
	}

	/**
	 * Compile the SELECT clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function selects(Query $query)
	{
		if ( ! is_null($query->aggregate)) return;

		$select = ($query->distinct) ? 'SELECT DISTINCT ' : 'SELECT ';

		return $select.$this->columnize($query->selects);
	}

	/**
	 * Compile an aggregating SELECT clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function aggregate(Query $query)
	{
		$column = $this->columnize($query->aggregate['columns']);

		if ($query->distinct and $column !== '*') $column = 'DISTINCT '.$column;

		return 'SELECT '.$query->aggregate['aggregator'].'('.$column.') AS '.$this->wrap('aggregate');
	}

	/**
	 * Compile the FROM clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function from(Query $query)
	{
		return 'FROM '.$this->wrap_table($query->from);
	}

	/**
	 * Compile the JOIN clauses for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function joins(Query $query)
	{
		// We need to iterate through each JOIN clause that is attached to the
		// query an translate it into SQL. The table and the columns will be
		// wrapped in identifiers to avoid naming collisions.
		//
		// Once all of the JOINs have been compiled, we can concatenate them
		// together using a single space, which should give us the complete
		// set of joins in valid SQL that can appended to the query.
		foreach ($query->joins as $join)
		{
			$table = $this->wrap_table($join->table);

			$clauses = array();

			// Each JOIN statement may have multiple clauses, so we will
			// iterate through each clause creating the conditions then
			// we will concatenate them all together.
			foreach ($join->clauses as $clause)
			{
				extract($clause);

				$column1 = $this->wrap($column1);

				$column2 = $this->wrap($column2);

				$clauses[] = "{$connector} {$column1} {$operator} {$column2}";
			}

			// The first clause will have a connector on the front,
			// but it is not needed on the first condition, so we
			// will strip it off of the condition before adding
			// it to the array of joins.
			$search = array('AND ', 'OR ');

			$clauses[0] = str_replace($search, '', $clauses[0]);

			$clauses = implode(' ', $clauses);

			$sql[] = "{$join->type} JOIN {$table} ON {$clauses}";
		}

		// Finally, we should have an array of JOIN clauses
		// that we can implode together and return as the
		// complete SQL for the JOIN of the query.
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
		if (is_null($query->wheres)) return '';

		// Each WHERE clause array has a "type" that is assigned by the query
		// builder, and each type has its own compiler function. We will call
		// the appropriate compiler for each where clause in the query.
		//
		// Keeping each particular where clause in its own "compiler" allows
		// us to keep the query generation process very granular, making it
		// easier to customize derived grammars for other databases.
		foreach ($query->wheres as $where)
		{
			$sql[] = $where['connector'].' '.$this->{$where['type']}($where);
		}

		if  (isset($sql))
		{
			// We attach the boolean connector to every where segment just
			// for convenience. Once we have built the entire clause we'll
			// remove the first instance of a connector from the clause.
			return 'WHERE '.preg_replace('/AND |OR /', '', implode(' ', $sql), 1);
		}
	}

	/**
	 * Compile a nested WHERE clause.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function where_nested($where)
	{
		return '('.substr($this->wheres($where['query']), 6).')';
	}

	/**
	 * Compile a simple WHERE clause.
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
	 * @param  array   $where
	 * @return string
	 */
	final protected function where_raw($where)
	{
		return $where['sql'];
	}

	/**
	 * Compile the GROUP BY clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function groupings(Query $query)
	{
		return 'GROUP BY '.$this->columnize($query->groupings);
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
			$direction = strtoupper($ordering['direction']);

			$sql[] = $this->wrap($ordering['column']).' '.$direction;
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
	 * This method handles the compilation of single row inserts and batch inserts.
	 *
	 * @param  Query   $query
	 * @param  array   $values
	 * @return string
	 */
	public function insert(Query $query, $values)
	{
		$table = $this->wrap_table($query->from);

		// Force every insert to be treated like a batch insert. This simply makes
		// creating the SQL syntax a little easier on us since we can always treat
		// the values as if it is an array containing multiple inserts.
		if ( ! is_array(reset($values))) $values = array($values);

		// Since we only care about the column names, we can pass any of the insert
		// arrays into the "columnize" method. The columns should be the same for
		// every insert to the table so we can just use the first record.
		$columns = $this->columnize(array_keys(reset($values)));

		// Build the list of parameter place-holders of values bound to the query.
		// Each insert should have the same number of bound paramters, so we can
		// just use the first array of values.
		$parameters = $this->parameterize(reset($values));

		$parameters = implode(', ', array_fill(0, count($values), "($parameters)"));

		return "INSERT INTO {$table} ({$columns}) VALUES {$parameters}";
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
		$table = $this->wrap_table($query->from);

		// Each column in the UPDATE statement needs to be wrapped in keyword
		// identifiers, and a place-holder needs to be created for each value
		// in the array of bindings. Of course, if the value of the binding
		// is an expression, the expression string will be injected.
		foreach ($values as $column => $value)
		{
			$columns[] = $this->wrap($column).' = '.$this->parameter($value);
		}

		$columns = implode(', ', $columns);

		// UPDATE statements may be constrained by a WHERE clause, so we'll
		// run the entire where compilation process for those contraints.
		// This is easily achieved by passing the query to the "wheres"
		// method which will call all of the where compilers.
		return trim("UPDATE {$table} SET {$columns} ".$this->wheres($query));
	}

	/**
	 * Compile a SQL DELETE statment from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function delete(Query $query)
	{
		$table = $this->wrap_table($query->from);

		// Like the UPDATE statement, the DELETE statement is constrained
		// by WHERE clauses, so we'll need to run the "wheres" method to
		// make the WHERE clauses for the query.
		return trim("DELETE FROM {$table} ".$this->wheres($query));
	}

	/**
	 * Transform an SQL short-cuts into real SQL for PDO.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return string
	 */
	public function shortcut($sql, $bindings)
	{
		// Laravel provides an easy short-cut notation for writing raw
		// WHERE IN statements. If (...) is in the query, it will be
		// replaced with the correct number of parameters based on
		// the bindings for the query.
		if (strpos($sql, '(...)') !== false)
		{
			for ($i = 0; $i < count($bindings); $i++)
			{
				// If the binding is an array, we can just assume it's
				// used to fill a "where in" condition, so we'll just
				// replace the next place-holder in the query.
				if (is_array($bindings[$i]))
				{
					$parameters = $this->parameterize($bindings[$i]);

					$sql = preg_replace('~\(\.\.\.\)~', "({$parameters})", $sql, 1);
				}
			}			
		}

		return trim($sql);
	}

}