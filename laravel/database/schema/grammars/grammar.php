<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Commands\Command;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Get the appropriate data type definition for the column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type(Column $column)
	{
		return $this->{'type_'.$column->type()}($column);
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
		if ($value instanceof Table or $value instanceof Column)
		{
			$value = $value->name;
		}

		return parent::wrap($value);
	}

}