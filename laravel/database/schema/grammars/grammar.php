<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Column;
use Laravel\Database\Schema\Columns\String;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Generate the SQL for a table creation command.
	 *
	 * @param  Table   $table
	 * @param  array   $command
	 * @return string
	 */
	abstract public function create(Table $table, $command);

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_string($column);

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_integer($column);

	/**
	 * Get the appropriate data type definition for the column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type(Column $column)
	{
		switch ($column->type())
		{
			case 'string':
				return $this->type_string($column);

			case 'integer':
				return $this->type_integer($column);

			default:
				throw new \Exception('Unknown column type ['.$column->type().'].');
		}
	}

}