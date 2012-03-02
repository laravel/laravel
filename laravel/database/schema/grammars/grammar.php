<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Fluent;
use Laravel\Database\Schema\Table;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Generate the SQL statement for creating a foreign key.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function foreign(Table $table, Fluent $command)
	{
		$name = $command->name;

		// We need to wrap both of the table names in quoted identifiers to protect
		// against any possible keyword collisions, both the table on which the
		// command is being executed and the referenced table are wrapped.
		$table = $this->wrap($table);

		$on = $this->wrap($command->on);

		// Next we need to columnize both the command table's columns as well as
		// the columns referenced by the foreign key. We'll cast the referenced
		// columns to an array since they aren't by the fluent command.
		$foreign = $this->columnize($command->columns);

		$referenced = $this->columnize((array) $command->references);

		$sql = "ALTER TABLE $table ADD CONSTRAINT $name ";

		return $sql .= "FOREIGN KEY ($foreign) REFERENCES $on ($referenced)";
	}

	/**
	 * Drop a constraint from the table.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $fluent
	 * @return string
	 */
	protected function drop_constraint(Table $table, Fluent $command)
	{
		return "ALTER TABLE ".$this->wrap($table)." DROP CONSTRAINT ".$command->name;
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
	 * Get the appropriate data type definition for the column.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type(Fluent $column)
	{
		return $this->{'type_'.$column->type}($column);
	}

}