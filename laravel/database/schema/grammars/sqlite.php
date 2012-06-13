<?php namespace Laravel\Database\Schema\Grammars;

use Laravel\Fluent;
use Laravel\Database\Schema\Table;

class SQLite extends Grammar {

	/**
	 * Generate the SQL statements for a table creation command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return array
	 */
	public function create(Table $table, Fluent $command)
	{
		$columns = implode(', ', $this->columns($table));

		// First we will generate the base table creation statement. Other than incrementing
		// keys, no indexes will be created during the first creation of the table since
		// they will be added in separate commands.
		$sql = 'CREATE TABLE '.$this->wrap($table).' ('.$columns;

		// SQLite does not allow adding a primary key as a command apart from the creation
		// of the table, so we'll need to sniff out any primary keys here and add them to
		// the table now during this command.
		$primary = array_first($table->commands, function($key, $value)
		{
			return $value->type == 'primary';
		});

		// If we found primary keys in the array of commands, we'll create the SQL for
		// the key addition and append it to the SQL table creation statement for
		// the schema table so the index is properly generated.
		if ( ! is_null($primary))
		{
			$columns = $this->columnize($primary->columns);

			$sql .= ", PRIMARY KEY ({$columns})";
		}

		return $sql .= ')';
	}

	/**
	 * Generate the SQL statements for a table modification command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return array
	 */
	public function add(Table $table, Fluent $command)
	{
		$columns = $this->columns($table);

		// Once we have the array of column definitions, we need to add "add" to the
		// front of each definition, then we'll concatenate the definitions
		// using commas like normal and generate the SQL.
		$columns = array_map(function($column)
		{
			return 'ADD COLUMN '.$column;

		}, $columns);

		// SQLite only allows one column to be added in an ALTER statement,
		// so we will create an array of statements and return them all to
		// the schema manager for separate execution.
		foreach ($columns as $column)
		{
			$sql[] = 'ALTER TABLE '.$this->wrap($table).' '.$column;
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
			// us keep the syntax easy and fluent, while translating the
			// types to the types used by the database.
			$sql = $this->wrap($column).' '.$this->type($column);

			$elements = array('nullable', 'defaults', 'incrementer');

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
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function nullable(Table $table, Fluent $column)
	{
		return ' NULL';
	}

	/**
	 * Get the SQL syntax for specifying a default value on a column.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function defaults(Table $table, Fluent $column)
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
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function incrementer(Table $table, Fluent $column)
	{
		if ($column->type == 'integer' and $column->increment)
		{
			return ' PRIMARY KEY AUTOINCREMENT';
		}
	}

	/**
	 * Generate the SQL statement for creating a unique index.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function unique(Table $table, Fluent $command)
	{
		return $this->key($table, $command, true);
	}

	/**
	 * Generate the SQL statement for creating a full-text index.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function fulltext(Table $table, Fluent $command)
	{
		$columns = $this->columnize($command->columns);

		return 'CREATE VIRTUAL TABLE '.$this->wrap($table)." USING fts4({$columns})";
	}

	/**
	 * Generate the SQL statement for creating a regular index.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function index(Table $table, Fluent $command)
	{
		return $this->key($table, $command);
	}

	/**
	 * Generate the SQL statement for creating a new index.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @param  bool    $unique
	 * @return string
	 */
	protected function key(Table $table, Fluent $command, $unique = false)
	{
		$columns = $this->columnize($command->columns);

		$create = ($unique) ? 'CREATE UNIQUE' : 'CREATE';

		return $create." INDEX {$command->name} ON ".$this->wrap($table)." ({$columns})";
	}

	/**
	 * Generate the SQL statement for a drop table command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function drop(Table $table, Fluent $command)
	{
		return 'DROP TABLE '.$this->wrap($table);
	}

	/**
	 * Generate the SQL statement for a drop unique key command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function drop_unique(Table $table, Fluent $command)
	{
		return $this->drop_key($table, $command);
	}

	/**
	 * Generate the SQL statement for a drop unique key command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	public function drop_index(Table $table, Fluent $command)
	{
		return $this->drop_key($table, $command);
	}

	/**
	 * Generate the SQL statement for a drop key command.
	 *
	 * @param  Table   $table
	 * @param  Fluent  $command
	 * @return string
	 */
	protected function drop_key(Table $table, Fluent $command)
	{
		return 'DROP INDEX '.$this->wrap($command->name);
	}

	/**
	 * Generate the data-type definition for a string.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_string(Fluent $column)
	{
		return 'VARCHAR';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_integer(Fluent $column)
	{
		return 'INTEGER';
	}

	/**
	 * Generate the data-type definition for an integer.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_float(Fluent $column)
	{
		return 'FLOAT';
	}

	/**
	 * Generate the data-type definintion for a decimal.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_decimal(Fluent $column)
	{
		return 'FLOAT';
	}

	/**
	 * Generate the data-type definition for a boolean.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_boolean(Fluent $column)
	{
		return 'INTEGER';
	}

	/**
	 * Generate the data-type definition for a date.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_date(Fluent $column)
	{
		return 'DATETIME';
	}

	/**
	 * Generate the data-type definition for a timestamp.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_timestamp(Fluent $column)
	{
		return 'DATETIME';
	}

	/**
	 * Generate the data-type definition for a text column.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_text(Fluent $column)
	{
		return 'TEXT';
	}

	/**
	 * Generate the data-type definition for a blob.
	 *
	 * @param  Fluent  $column
	 * @return string
	 */
	protected function type_blob(Fluent $column)
	{
		return 'BLOB';
	}

}