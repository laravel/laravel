<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Commands\Command;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Generate the SQL for a table creation command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	abstract public function create(Table $table, Command $command);

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
		$type = $column->type();

		if (method_exists($this, 'type_'.$type))
		{
			return $this->{'type_'.$type}($column);
		}

		throw new \Exception('Unknown column type ['.$column->type().'].');
	}

}