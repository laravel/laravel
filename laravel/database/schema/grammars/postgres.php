<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Commands\Command;

class Postgres extends Grammar {

	/**
	 * Generate the SQL statements for a table creation command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return array
	 */
	public function create(Table $table, Command $command)
	{
		$columns = implode(', ', $this->columns($table));

		// First we will generate the base table creation statement. Other than
		// auto-incrementing keys, no indexes will be created during the first
		// creation of the table. They will be added in separate commands.
		$sql = 'CREATE TABLE '.$this->wrap($table).' ('.$columns.')';

		return $sql;
	}

	/**
	 * Geenrate the SQL statements for a table modification command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return array
	 */
	public function add(Table $table, Command $command)
	{
		$columns = $this->columns($table);

		// Once we the array of column definitions, we'll add "add column"
		// to the front of each definition, then we'll concatenate the
		// definitions using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'ADD COLUMN '.$column;

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table).' '.$columns;
	}

	/**
	 * Create the individual column definitions for the table.
	 *
	 * @param  Table  $table
	 * @return array
	 */
	protected function columns(Table $table)
	{
		$columns = array();

		foreach ($table->columns as $column)
		{
			// Each of the data type's have their own definition creation method,
			// which is responsible for creating the SQL for the type. This lets
			// us to keep the syntax easy and fluent, while translating the
			// types to the types used by the database system.
			$sql = $this->wrap($column).' '.$this->type($column);

			$elements = array('incrementer', 'nullable', 'default_value');

			foreach ($elements as $element)
			{
				$sql .= $this->$element($table, $column);
			}

			$columns[] = $sql;
		}

		return $columns;
	}

	/**
	 * Get the SQL syntax for indicating if a column is nullable.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function nullable(Table $table, Column $column)
	{
		return ($column->nullable) ? ' NULL' : ' NOT NULL';
	}

	/**
	 * Get the SQL syntax for specifying a default value on a column.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function default_value(Table $table, Column $column)
	{
		if ( ! is_null($column->default))
		{
			return " DEFAULT '".$column->default."'";
		}
	}

	/**
	 * Get the SQL syntax for defining an auto-incrementing column.
	 *
	 * @param  Table   $table
	 * @param  Column  $column
	 * @return string
	 */
	protected function incrementer(Table $table, Column $column)
	{
		// We don't actually need to specify an "auto_increment" keyword since
		// we handle the auto-increment definition in the type definition for
		// integers by changing the type to "serial", which is a convenient
		// notational short-cut provided by Postgres.
		if ($column->type() == 'integer' and $column->increment)
		{
			return ' PRIMARY KEY';
		}
	}

	/**
	 * Generate the SQL statement for creating a primary key.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function primary(Table $table, Command $command)
	{
		$columns = $this->columnize($command->columns);

		return 'ALTER TABLE '.$this->wrap($table)." ADD PRIMARY KEY ({$columns})";
	}

	/**
	 * Generate the SQL statement for creating a unique index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function unique(Table $table, Command $command)
	{
		return $this->key($table, $command, true);
	}

	/**
	 * Generate the SQL statement for creating a full-text index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function fulltext(Table $table, Command $command)
	{
		$name = $command->name;

		$columns = $this->columnize($command->columns);

		return "CREATE INDEX {$name} ON ".$this->wrap($table)." USING gin({$columns})";
	}

	/**
	 * Generate the SQL statement for creating a regular index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function index(Table $table, Command $command)
	{
		return $this->key($table, $command);
	}

	/**
	 * Generate the SQL statement for creating a new index.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @param  bool     $unique
	 * @return string
	 */
	protected function key(Table $table, Command $command, $unique = false)
	{
		$columns = $this->columnize($command->columns);

		$create = ($unique) ? 'CREATE UNIQUE' : 'CREATE';

		return $create." INDEX {$command->name} ON ".$this->wrap($table)." ({$columns})";
	}

	/**
	 * Generate the SQL statement for a drop table command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop(Table $table, Command $command)
	{
		return 'DROP TABLE '.$this->wrap($table);
	}

	/**
	 * Generate the SQL statement for a drop column command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop_column(Table $table, Command $command)
	{
		$columns = array_map(array($this, 'wrap'), $command->columns);

		// Once we have wrapped all of the columns, we'll add "drop_column"
		// to the front of each column name, then we'll concatenate the
		// columns using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'DROP COLUMN '.$column.' RESTRICT';

		}, $columns));

		return 'ALTER TABLE '.$this->wrap($table).' '.$columns;
	}

	/**
	 * Generate the SQL statement for a drop index command.
	 *
	 * @param  Table    $table
	 * @param  Command  $command
	 * @return string
	 */
	public function drop_index(Table $table, Command $command)
	{
		$name = $this->wrap($command->name);

		return "DROP INDEX {$name}";
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_string($column)
	{
		return 'VARCHAR('.$column->length.')';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_integer($column)
	{
		return ($column->increment) ? 'SERIAL' : 'INTEGER';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_float($column)
	{
		return 'REAL';
	}

	/**
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_boolean($column)
	{
		return 'SMALLINT';
	}

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_date($column)
	{
		return 'TIMESTAMP';
	}

	/**
	 * Generate the data-type definition for a timestamp.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_timestamp($column)
	{
		return 'TIMESTAMP';
	}

	/**
	 * Generate the data-type definition for a text column.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_text($column)
	{
		return 'TEXT';
	}

	/**
	 * Generate the data-type definition for a blob.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_blob($column)
	{
		return 'BYTEA';
	}

}