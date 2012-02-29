<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Fluent;
use Laravel\Database\Schema\Table;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Get the appropriate data type definition for the column.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type(Fluent $column)
	{
		return $this->{'type_'.$column->type}($column);
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  Table|string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		// This method is primarily for convenience so we can just pass a
		// column or table instance into the wrap method without sending
		// in the name each time we need to wrap one of these objects.
		if ($value instanceof Table)
		{
			return $this->wrap_table($value->name);
		}
		elseif ($value instanceof Fluent)
		{
			$value = $value->name;
		}

		return parent::wrap($value);
	}

	/**
	 * Get the SQL syntax for indicating if a column is unsigned.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function unsigned(Table $table, Fluent $column)
	{
		if ($column->type == 'integer' && $column->unsigned)
		{
			return ' UNSIGNED';
		}
	}

}