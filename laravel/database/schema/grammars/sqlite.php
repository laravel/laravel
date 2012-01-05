<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Database\Schema\Table;
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

		$sql .= ')';

		return (array) $sql;
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

		// Once we the array of column definitions, we need to add "add"
		// to the front of each definition, then we'll concatenate the
		// definitions using commas like normal and generate the SQL.
		$columns = implode(', ', array_map(function($column)
		{
			return 'ADD '.$column;

		}, $columns));

		$sql = 'ALTER TABLE '.$this->wrap($table->name).' '.$columns;

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
			// Each of the data type's have their own definition creation
			// method, which is responsible for creating the SQL version
			// of the data type. This allows us to keep the syntax easy
			// and fluent, while translating the types to the types
			// used by the database system.
			$sql = $this->wrap($column->name).' '.$this->type($column);

			$sql .= ($column->nullable) ? ' NULL' : ' NOT NULL';

			// Auto-incrementing IDs are required to be a primary key,
			// so we'll go ahead and add the primary key definition
			// when the column is created.
			if ($column->type() == 'integer' and $column->increment)
			{
				$sql .= ' PRIMARY KEY AUTOINCREMENT';
			}

			$columns[] = $sql;
		}

		return $columns;
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

		// SQLite indexes are required to have a name, so we'll generate a
		// unique one by concatenating the table name and the names of all
		// of the columns being added to the index.
		$name = $table->name.'_'.implode('_', $command->columns);

		return $create." INDEX {$name} ON ".$this->wrap($table->name)." ({$columns})";
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