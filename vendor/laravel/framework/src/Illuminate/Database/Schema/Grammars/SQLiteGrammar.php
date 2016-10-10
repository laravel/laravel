<?php namespace Illuminate\Database\Schema\Grammars;

use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;

class SQLiteGrammar extends Grammar {

	/**
	 * The possible column modifiers.
	 *
	 * @var array
	 */
	protected $modifiers = array('Nullable', 'Default', 'Increment');

	/**
	 * The columns available as serials.
	 *
	 * @var array
	 */
	protected $serials = array('bigInteger', 'integer');

	/**
	 * Compile the query to determine if a table exists.
	 *
	 * @return string
	 */
	public function compileTableExists()
	{
		return "select * from sqlite_master where type = 'table' and name = ?";
	}

	/**
	 * Compile the query to determine the list of columns.
	 *
	 * @param  string  $table
	 * @return string
	 */
	public function compileColumnExists($table)
	{
		return 'pragma table_info('.str_replace('.', '__', $table).')';
	}

	/**
	 * Compile a create table command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileCreate(Blueprint $blueprint, Fluent $command)
	{
		$columns = implode(', ', $this->getColumns($blueprint));

		$sql = 'create table '.$this->wrapTable($blueprint)." ($columns";

		// SQLite forces primary keys to be added when the table is initially created
		// so we will need to check for a primary key commands and add the columns
		// to the table's declaration here so they can be created on the tables.
		$sql .= (string) $this->addForeignKeys($blueprint);

		$sql .= (string) $this->addPrimaryKeys($blueprint);

		return $sql .= ')';
	}

	/**
	 * Get the foreign key syntax for a table creation statement.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @return string|null
	 */
	protected function addForeignKeys(Blueprint $blueprint)
	{
		$sql = '';

		$foreigns = $this->getCommandsByName($blueprint, 'foreign');

		// Once we have all the foreign key commands for the table creation statement
		// we'll loop through each of them and add them to the create table SQL we
		// are building, since SQLite needs foreign keys on the tables creation.
		foreach ($foreigns as $foreign)
		{
			$sql .= $this->getForeignKey($foreign);

			if ( ! is_null($foreign->onDelete))
			{
				$sql .= " on delete {$foreign->onDelete}";
			}

			if ( ! is_null($foreign->onUpdate))
			{
				$sql .= " on update {$foreign->onUpdate}";
			}
		}

		return $sql;
	}

	/**
	 * Get the SQL for the foreign key.
	 *
	 * @param  \Illuminate\Support\Fluent  $foreign
	 * @return string
	 */
	protected function getForeignKey($foreign)
	{
		$on = $this->wrapTable($foreign->on);

		// We need to columnize the columns that the foreign key is being defined for
		// so that it is a properly formatted list. Once we have done this, we can
		// return the foreign key SQL declaration to the calling method for use.
		$columns = $this->columnize($foreign->columns);

		$onColumns = $this->columnize((array) $foreign->references);

		return ", foreign key($columns) references $on($onColumns)";
	}

	/**
	 * Get the primary key syntax for a table creation statement.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @return string|null
	 */
	protected function addPrimaryKeys(Blueprint $blueprint)
	{
		$primary = $this->getCommandByName($blueprint, 'primary');

		if ( ! is_null($primary))
		{
			$columns = $this->columnize($primary->columns);

			return ", primary key ({$columns})";
		}
	}

	/**
	 * Compile alter table commands for adding columns
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return array
	 */
	public function compileAdd(Blueprint $blueprint, Fluent $command)
	{
		$table = $this->wrapTable($blueprint);

		$columns = $this->prefixArray('add column', $this->getColumns($blueprint));

		foreach ($columns as $column)
		{
			$statements[] = 'alter table '.$table.' '.$column;
		}

		return $statements;
	}

	/**
	 * Compile a unique key command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileUnique(Blueprint $blueprint, Fluent $command)
	{
		$columns = $this->columnize($command->columns);

		$table = $this->wrapTable($blueprint);

		return "create unique index {$command->index} on {$table} ({$columns})";
	}

	/**
	 * Compile a plain index key command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileIndex(Blueprint $blueprint, Fluent $command)
	{
		$columns = $this->columnize($command->columns);

		$table = $this->wrapTable($blueprint);

		return "create index {$command->index} on {$table} ({$columns})";
	}

	/**
	 * Compile a foreign key command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileForeign(Blueprint $blueprint, Fluent $command)
	{
		// Handled on table creation...
	}

	/**
	 * Compile a drop table command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileDrop(Blueprint $blueprint, Fluent $command)
	{
		return 'drop table '.$this->wrapTable($blueprint);
	}

	/**
	 * Compile a drop table (if exists) command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileDropIfExists(Blueprint $blueprint, Fluent $command)
	{
		return 'drop table if exists '.$this->wrapTable($blueprint);
	}

	/**
	 * Compile a drop column command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @param  \Illuminate\Database\Connection  $connection
	 * @return array
	 */
	public function compileDropColumn(Blueprint $blueprint, Fluent $command, Connection $connection)
	{
		$schema = $connection->getDoctrineSchemaManager();

		$tableDiff = $this->getDoctrineTableDiff($blueprint, $schema);

		foreach ($command->columns as $name)
		{
			$column = $connection->getDoctrineColumn($blueprint->getTable(), $name);

			$tableDiff->removedColumns[$name] = $column;
		}

		return (array) $schema->getDatabasePlatform()->getAlterTableSQL($tableDiff);
	}

	/**
	 * Compile a drop unique key command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileDropUnique(Blueprint $blueprint, Fluent $command)
	{
		return "drop index {$command->index}";
	}

	/**
	 * Compile a drop index command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileDropIndex(Blueprint $blueprint, Fluent $command)
	{
		return "drop index {$command->index}";
	}

	/**
	 * Compile a rename table command.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $command
	 * @return string
	 */
	public function compileRename(Blueprint $blueprint, Fluent $command)
	{
		$from = $this->wrapTable($blueprint);

		return "alter table {$from} rename to ".$this->wrapTable($command->to);
	}

	/**
	 * Create the column definition for a char type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeChar(Fluent $column)
	{
		return 'varchar';
	}

	/**
	 * Create the column definition for a string type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeString(Fluent $column)
	{
		return 'varchar';
	}

	/**
	 * Create the column definition for a text type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeText(Fluent $column)
	{
		return 'text';
	}

	/**
	 * Create the column definition for a medium text type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeMediumText(Fluent $column)
	{
		return 'text';
	}

	/**
	 * Create the column definition for a long text type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeLongText(Fluent $column)
	{
		return 'text';
	}

	/**
	 * Create the column definition for a integer type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeInteger(Fluent $column)
	{
		return 'integer';
	}

	/**
	 * Create the column definition for a big integer type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeBigInteger(Fluent $column)
	{
		return 'integer';
	}

	/**
	 * Create the column definition for a medium integer type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeMediumInteger(Fluent $column)
	{
		return 'integer';
	}

	/**
	 * Create the column definition for a tiny integer type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeTinyInteger(Fluent $column)
	{
		return 'integer';
	}

	/**
	 * Create the column definition for a small integer type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeSmallInteger(Fluent $column)
	{
		return 'integer';
	}

	/**
	 * Create the column definition for a float type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeFloat(Fluent $column)
	{
		return 'float';
	}

	/**
	 * Create the column definition for a double type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeDouble(Fluent $column)
	{
		return 'float';
	}

	/**
	 * Create the column definition for a decimal type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeDecimal(Fluent $column)
	{
		return 'float';
	}

	/**
	 * Create the column definition for a boolean type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeBoolean(Fluent $column)
	{
		return 'tinyint';
	}

	/**
	 * Create the column definition for an enum type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeEnum(Fluent $column)
	{
		return 'varchar';
	}

	/**
	 * Create the column definition for a date type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeDate(Fluent $column)
	{
		return 'date';
	}

	/**
	 * Create the column definition for a date-time type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeDateTime(Fluent $column)
	{
		return 'datetime';
	}

	/**
	 * Create the column definition for a time type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeTime(Fluent $column)
	{
		return 'time';
	}

	/**
	 * Create the column definition for a timestamp type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeTimestamp(Fluent $column)
	{
		return 'datetime';
	}

	/**
	 * Create the column definition for a binary type.
	 *
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string
	 */
	protected function typeBinary(Fluent $column)
	{
		return 'blob';
	}

	/**
	 * Get the SQL for a nullable column modifier.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string|null
	 */
	protected function modifyNullable(Blueprint $blueprint, Fluent $column)
	{
		return $column->nullable ? ' null' : ' not null';
	}

	/**
	 * Get the SQL for a default column modifier.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string|null
	 */
	protected function modifyDefault(Blueprint $blueprint, Fluent $column)
	{
		if ( ! is_null($column->default))
		{
			return " default ".$this->getDefaultValue($column->default);
		}
	}

	/**
	 * Get the SQL for an auto-increment column modifier.
	 *
	 * @param  \Illuminate\Database\Schema\Blueprint  $blueprint
	 * @param  \Illuminate\Support\Fluent  $column
	 * @return string|null
	 */
	protected function modifyIncrement(Blueprint $blueprint, Fluent $column)
	{
		if (in_array($column->type, $this->serials) && $column->autoIncrement)
		{
			return ' primary key autoincrement';
		}
	}

}
