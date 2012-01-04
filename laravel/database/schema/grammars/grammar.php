<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Columns\String;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Generate the SQL for a table creation command.
	 *
	 * @param  Table   $table
	 * @return string
	 */
	abstract public function create(Table $table);

	/**
	 * Get the appropriate data type definition for the column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type(Column $column)
	{
		if ($column instanceof String)
		{
			return $this->type_string($column);
		}
		else
		{
			throw new \Exception("Unknown column type encountered by grammar.");
		}
	}

}