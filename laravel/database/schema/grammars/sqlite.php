<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
use Laravel\Database\Schema\Columns\Column;
use Laravel\Database\Schema\Commands\Command;
use Laravel\Database\Schema\Commands\Primary;

class SQLite extends Grammar {

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
		$sql = 'CREATE TABLE '.$this->wrap($table->name).' ('.$columns;

		// SQLite does not allow adding a primary key as a command apart from
		// when the table is initially created, so we'll need to sniff out
		// any primary keys here and add them to the table.
		//
		// Because of this, this class does not have the typical "primary"
		// method as it would be pointless since the primary keys can't
		// be set on anything but the table creation statement.
		$primary = array_first($table->commands, function($key, $value)
		{
			return $value instanceof Primary;
		});

		// If we found primary key in the array of commands, we'll create
		// the SQL for the key addition and append it to the SQL table
		// creation statement for the schema table.
		if ( ! is_null($primary))
		{
			$columns = $this->columnize($primary->columns);

			$sql .= ", PRIMARY KEY ({$columns})";
		}

		return $sql .= ')';
	}

	/**
	 * Geenrate the SQL statements for a table modification command.
	 *
	 * @param  Table   $table
	 * @param  Command  $command
	 * @return array
	 */
	public function add(Table $table, Command $command)
	{
		$columns = $this->columns($table);

		// Once we have an array of all of the column definitions, we need to
		// spin through each one and prepend "ADD COLUMN" to each of them,
		// which is the syntax used by SQLite when adding columns.
		$columns = array_map(function($column)
		{
			return 'ADD COLUMN '.$column;

		}, $columns);

		// SQLite only allows one column to be added in an ALTER statement,
		// so we will create an array of statements and return them all to
		// the schema manager, which will execute each one.
		foreach ($columns as $column)
		{
			$sql[] = 'ALTER TABLE '.$this->wrap($table->name).' '.$column;
		}

		return (array) $sql;
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
			// Each of the data type's have their own definition creation method
			// which is responsible for creating the SQL for the type. This lets
			// us to keep the syntax easy and fluent, while translating the
			// types to the types used by the database system.
			$sql = $this->wrap($column->name).' '.$this->type($column);

			$elements = array('nullable', 'default_value', 'incrementer');

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
			return ' DEFAULT '.$this->wrap($column->default);
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
		if ($column->type() == 'integer' and $column->increment)
		{
			return ' PRIMARY KEY AUTOINCREMENT';
		}
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
		$columns = $this->columnize($command->columns);

		return 'CREATE VIRTUAL TABLE '.$this->wrap($table->name)." USING fts4({$columns})";
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

		return $create." INDEX {$command->name} ON ".$this->wrap($table->name)." ({$columns})";
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
		return 'DROP TABLE '.$this->wrap($table->name);
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
		return 'DROP INDEX '.$this->wrap($command->name);
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_string($column)
	{
		return 'VARCHAR';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_integer($column)
	{
		return 'INTEGER';
	}

	/**
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_boolean($column)
	{
		return 'INTEGER';
	}

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Column  $column
	 * @return string
	 */
	protected function type_date($column)
	{
		return 'DATETIME';
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
		return 'BLOB';
	}

}