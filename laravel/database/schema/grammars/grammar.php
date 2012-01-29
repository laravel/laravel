<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Fluent;
use Laravel\Database\Schema\Table;

abstract class Grammar extends \Laravel\Database\Grammar {

	/**
	 * Create a new Grammar instance.
	 *
	 * @param  Connection    $connection
	 * @return void
	 */
	public function __construct(\Laravel\Database\Connection $connection)
	{
		$this->connection = $connection;
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
		if ($value instanceof Table or $value instanceof Fluent)
		{
			$value = $value->name;
		}

		return parent::wrap($value);
	}

	/**
	 * Wrap a table in keyword identifiers after adding the prefix.
	 *
	 * @param  Table|string  $value
	 * @return string
	 */
	public function wrap_table($value)
	{
		// This method is primarily for convenience so we can just pass a
		// column or table instance into the wrap method without sending
		// in the name each time we need to wrap one of these objects.
		if ($value instanceof Table or $value instanceof Fluent)
		{
			$value = $value->name;
		}

		return parent::wrap($this->connection->table_prefix().$value);
	}

}