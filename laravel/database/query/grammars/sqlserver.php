<?php namespace Laravel\Database\Query\Grammars;

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
			$select .= 'TOP '.$query->limit;
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
		if ( ! isset($components['orderings']))
		{
			$components['orderings'] = 'ORDER BY (SELECT 0)';
		}

		$components['selects'] .= ", ROW_NUMBER() OVER ({$components['orderings']}) AS RowNum";

		$sql = $this->concatenate($components);

		return ';WITH Results_CTE AS ('.$sql.') SELECT * FROM Results_CTE WHERE RowNum >= '.$query->offset.' AND RowNum < '.$query->offset + $query->limit;
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