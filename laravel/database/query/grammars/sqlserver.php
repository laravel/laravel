<?php namespace Laravel\Database\Query\Grammars;

use Laravel\Database\Query;

class SQLServer extends Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '[%s]';

	/**
	 * Compile a SQL SELECT statement from a Query instance.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	public function select(Query $query)
	{
		$sql = parent::components($query);

		// SQL Server does not currently implement an "OFFSET" type keyword, so we
		// actually have to generate the ANSI standard SQL for doing offset like
		// functionality. In the next version of SQL Server, an OFFSET like
		// keyword is included for convenience.
		if ($query->offset > 0)
		{
			return $this->ansi_offset($query, $sql);
		}

		// Once all of the clauses have been compiled, we can join them all as
		// one statement. Any segments that are null or an empty string will
		// be removed from the array of clauses before they are imploded.
		return $this->concatenate($sql);
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

		// Instead of using a "LIMIT" keyword, SQL Server uses the "TOP"
		// keyword within the SELECT statement. So, if we have a limit,
		// we will add it here.
		//
		// We will not add the TOP clause if there is an offset however,
		// since we will have to handle offsets using the ANSI syntax
		// and will need to remove the TOP clause in that situation.
		if ($query->limit > 0 and $query->offset <= 0)
		{
			$select .= 'TOP '.$query->limit.' ';
		}

		return $select.$this->columnize($query->selects);
	}

	/**
	 * Generate the ANSI standard SQL for an offset clause.
	 *
	 * @param  Query  $query
	 * @param  array  $components
	 * @return array
	 */
	protected function ansi_offset(Query $query, $components)
	{
		// An ORDER BY clause is required to make this offset query
		// work, so if one doesn't exist, we'll just create a dummy
		// clause to satisfy the database.
		if ( ! isset($components['orderings']))
		{
			$components['orderings'] = 'ORDER BY (SELECT 0)';
		}

		// We need to add the row number to the query results so we
		// can compare it against the offset and limit values given
		// for the statement. To do that we'll add an expression to
		// the select statement for the row number.
		$orderings = $components['orderings'];

		$components['selects'] .= ", ROW_NUMBER() OVER ({$orderings}) AS RowNum";

		unset($components['orderings']);

		$start = $query->offset + 1;

		// Next we need to calculate the constraint that should be
		// placed on the row number to get the correct offset and
		// limit on the query. If a limit has not been set, we'll
		// only add a constraint to handle offset.
		if ($query->limit > 0)
		{
			$finish = $query->offset + $query->limit;

			$constraint = "BETWEEN {$start} AND {$finish}";
		}
		else
		{
			$constraint = ">= {$start}";
		}

		// Now, we're finally ready to build the final SQL query.
		// We'll create a common table expression with the query
		// and then select all of the results from it where the
		// row number is between oru given limit and offset.
		$sql = $this->concatenate($components);

		return "SELECT * FROM ($sql) AS TempTable WHERE RowNum {$constraint}";
	}

	/**
	 * Compile the LIMIT clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function limit(Query $query)
	{
		return;
	}

	/**
	 * Compile the OFFSET clause for a query.
	 *
	 * @param  Query   $query
	 * @return string
	 */
	protected function offset(Query $query)
	{
		return;
	}

}