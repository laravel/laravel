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
	 * Geenrate the SQL statements for a table modification command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return array
	 */
	abstract public function add(Table $table, Command $command);

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
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_boolean($column);

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_date($column);

	/**
	 * Generate the data-type definition for a text column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_text($column);

	/**
	 * Generate the data-type definition for a blob.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	abstract protected function type_blob($column);

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

}