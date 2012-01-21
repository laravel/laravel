<?php namespace Laravel\Database;

abstract class Grammar {

	/**
	 * The keyword identifier for the database system.
	 *
	 * @var string
	 */
	protected $wrapper = '"%s"';

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		// Expressions should be injected into the query as raw strings,
		// so we do not want to wrap them in any way. We'll just return
		// the string value from the expression to be included.
		if ($value instanceof Expression) return $value->get();

		// If the value being wrapped contains a column alias, we need to
		// wrap it a little differently as each segment must be wrapped
		// and not the entire string. We'll split the value on the "as"
		// joiner to extract the column and the alias.
		if (strpos(strtolower($value), ' as ') !== false)
		{
			$segments = explode(' ', $value);

			return $this->wrap($segments[0]).' AS '.$this->wrap($segments[2]);
		}

		// Since columns may be prefixed with their corresponding table
		// name so as to not make them ambiguous, we will need to wrap
		// the table and the column in keyword identifiers.
		foreach (explode('.', $value) as $segment)
		{
			if ($segment == '*')
			{
				$wrapped[] = $segment;
			}
			else
			{
				$wrapped[] = sprintf($this->wrapper, $segment);
			}
		}

		return implode('.', $wrapped);
	}

	/**
	 * Create query parameters from an array of values.
	 *
	 * <code>
	 *		Returns "?, ?, ?", which may be used as PDO place-holders
	 *		$parameters = $grammar->parameterize(array(1, 2, 3));
	 *
	 *		// Returns "?, "Taylor"" since an expression is used
	 *		$parameters = $grammar->parameterize(array(1, DB::raw('Taylor')));
	 * </code>
	 *
	 * @param  array   $values
	 * @return string
	 */
	final public function parameterize($values)
	{
		return implode(', ', array_map(array($this, 'parameter'), $values));
	}

	/**
	 * Get the appropriate query parameter string for a value.
	 *
	 * <code>
	 *		// Returns a "?" PDO place-holder
	 *		$value = $grammar->parameter('Taylor Otwell');
	 *
	 *		// Returns "Taylor Otwell" as the raw value of the expression
	 *		$value = $grammar->parameter(DB::raw('Taylor Otwell'));
	 * </code>
	 *
	 * @param  mixed   $value
	 * @return string
	 */
	final public function parameter($value)
	{
		return ($value instanceof Expression) ? $value->get() : '?';
	}

	/**
	 * Create a comma-delimited list of wrapped column names.
	 *
	 * <code>
	 *		// Returns ""Taylor", "Otwell"" when the identifier is quotes
	 *		$columns = $grammar->columnize(array('Taylor', 'Otwell'));
	 * </code>
	 *
	 * @param  array   $columns
	 * @return string
	 */
	final public function columnize($columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

}