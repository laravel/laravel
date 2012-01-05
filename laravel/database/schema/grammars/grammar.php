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
		if ($value instanceof Table or $value instanceof Column)
		{
			$value = $value->name;
		}

		return parent::wrap($value);
	}

}