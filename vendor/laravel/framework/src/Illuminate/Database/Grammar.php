<?php namespace Illuminate\Database;

abstract class Grammar {

	/**
	 * The grammar table prefix.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * Wrap an array of values.
	 *
	 * @param  array  $values
	 * @return array
	 */
	public function wrapArray(array $values)
	{
		return array_map(array($this, 'wrap'), $values);
	}

	/**
	 * Wrap a table in keyword identifiers.
	 *
	 * @param  string  $table
	 * @return string
	 */
	public function wrapTable($table)
	{
		if ($this->isExpression($table)) return $this->getValue($table);

		return $this->wrap($this->tablePrefix.$table);
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		if ($this->isExpression($value)) return $this->getValue($value);

		// If the value being wrapped has a column alias we will need to separate out
		// the pieces so we can wrap each of the segments of the expression on it
		// own, and then joins them both back together with the "as" connector.
		if (strpos(strtolower($value), ' as ') !== false)
		{
			$segments = explode(' ', $value);

			return $this->wrap($segments[0]).' as '.$this->wrap($segments[2]);
		}

		$wrapped = array();

		$segments = explode('.', $value);

		// If the value is not an aliased table expression, we'll just wrap it like
		// normal, so if there is more than one segment, we will wrap the first
		// segments as if it was a table and the rest as just regular values.
		foreach ($segments as $key => $segment)
		{
			if ($key == 0 && count($segments) > 1)
			{
				$wrapped[] = $this->wrapTable($segment);
			}
			else
			{
				$wrapped[] = $this->wrapValue($segment);
			}
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value)
	{
		if ($value === '*') return $value;

		return '"'.str_replace('"', '""', $value).'"';
	}

	/**
	 * Convert an array of column names into a delimited string.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	public function columnize(array $columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

	/**
	 * Create query parameter place-holders for an array.
	 *
	 * @param  array   $values
	 * @return string
	 */
	public function parameterize(array $values)
	{
		return implode(', ', array_map(array($this, 'parameter'), $values));
	}

	/**
	 * Get the appropriate query parameter place-holder for a value.
	 *
	 * @param  mixed   $value
	 * @return string
	 */
	public function parameter($value)
	{
		return $this->isExpression($value) ? $this->getValue($value) : '?';
	}

	/**
	 * Get the value of a raw expression.
	 *
	 * @param  \Illuminate\Database\Query\Expression  $expression
	 * @return string
	 */
	public function getValue($expression)
	{
		return $expression->getValue();
	}

	/**
	 * Determine if the given value is a raw expression.
	 *
	 * @param  mixed  $value
	 * @return bool
	 */
	public function isExpression($value)
	{
		return $value instanceof Query\Expression;
	}

	/**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	public function getDateFormat()
	{
		return 'Y-m-d H:i:s';
	}

	/**
	 * Get the grammar's table prefix.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the grammar's table prefix.
	 *
	 * @param  string  $prefix
	 * @return \Illuminate\Database\Grammar
	 */
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;

		return $this;
	}

}
