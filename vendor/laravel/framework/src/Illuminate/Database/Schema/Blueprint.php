<?php namespace Illuminate\Database\Schema;

use Closure;
use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;

class Blueprint {

	/**
	 * The table the blueprint describes.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The columns that should be added to the table.
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * The commands that should be run for the table.
	 *
	 * @var array
	 */
	protected $commands = array();

	/**
	 * The storage engine that should be used for the table.
	 *
	 * @var string
	 */
	public $engine;

	/**
	 * Create a new schema blueprint.
	 *
	 * @param  string   $table
	 * @param  Closure  $callback
	 * @return void
	 */
	public function __construct($table, Closure $callback = null)
	{
		$this->table = $table;

		if ( ! is_null($callback)) $callback($this);
	}

	/**
	 * Execute the blueprint against the database.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @param  \Illuminate\Database\Schema\Grammars\Grammar $grammar
	 * @return void
	 */
	public function build(Connection $connection, Grammar $grammar)
	{
		foreach ($this->toSql($connection, $grammar) as $statement)
		{
			$connection->statement($statement);
		}
	}

	/**
	 * Get the raw SQL statements for the blueprint.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @param  \Illuminate\Database\Schema\Grammars\Grammar  $grammar
	 * @return array
	 */
	public function toSql(Connection $connection, Grammar $grammar)
	{
		$this->addImpliedCommands();

		$statements = array();

		// Each type of command has a corresponding compiler function on the schema
		// grammar which is used to build the necessary SQL statements to build
		// the blueprint element, so we'll just call that compilers function.
		foreach ($this->commands as $command)
		{
			$method = 'compile'.ucfirst($command->name);

			if (method_exists($grammar, $method))
			{
				if ( ! is_null($sql = $grammar->$method($this, $command, $connection)))
				{
					$statements = array_merge($statements, (array) $sql);
				}
			}
		}

		return $statements;
	}

	/**
	 * Add the commands that are implied by the blueprint.
	 *
	 * @return void
	 */
	protected function addImpliedCommands()
	{
		if (count($this->columns) > 0 && ! $this->creating())
		{
			array_unshift($this->commands, $this->createCommand('add'));
		}

		$this->addFluentIndexes();
	}

	/**
	 * Add the index commands fluently specified on columns.
	 *
	 * @return void
	 */
	protected function addFluentIndexes()
	{
		foreach ($this->columns as $column)
		{
			foreach (array('primary', 'unique', 'index') as $index)
			{
				// If the index has been specified on the given column, but is simply
				// equal to "true" (boolean), no name has been specified for this
				// index, so we will simply call the index methods without one.
				if ($column->$index === true)
				{
					$this->$index($column->name);

					continue 2;
				}

				// If the index has been specified on the column and it is something
				// other than boolean true, we will assume a name was provided on
				// the index specification, and pass in the name to the method.
				elseif (isset($column->$index))
				{
					$this->$index($column->name, $column->$index);

					continue 2;
				}
			}
		}
	}

	/**
	 * Determine if the blueprint has a create command.
	 *
	 * @return bool
	 */
	protected function creating()
	{
		foreach ($this->commands as $command)
		{
			if ($command->name == 'create') return true;
		}

		return false;
	}

	/**
	 * Indicate that the table needs to be created.
	 *
	 * @return \Illuminate\Support\Fluent
	 */
	public function create()
	{
		return $this->addCommand('create');
	}

	/**
	 * Indicate that the table should be dropped.
	 *
	 * @return \Illuminate\Support\Fluent
	 */
	public function drop()
	{
		return $this->addCommand('drop');
	}

	/**
	 * Indicate that the table should be dropped if it exists.
	 *
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropIfExists()
	{
		return $this->addCommand('dropIfExists');
	}

	/**
	 * Indicate that the given columns should be dropped.
	 *
	 * @param  string|array  $columns
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropColumn($columns)
	{
		$columns = is_array($columns) ? $columns : (array) func_get_args();

		return $this->addCommand('dropColumn', compact('columns'));
	}

	/**
	 * Indicate that the given columns should be renamed.
	 *
	 * @param  string  $from
	 * @param  string  $to
	 * @return \Illuminate\Support\Fluent
	 */
	public function renameColumn($from, $to)
	{
		return $this->addCommand('renameColumn', compact('from', 'to'));
	}

	/**
	 * Indicate that the given primary key should be dropped.
	 *
	 * @param  string|array  $index
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropPrimary($index = null)
	{
		return $this->dropIndexCommand('dropPrimary', 'primary', $index);
	}

	/**
	 * Indicate that the given unique key should be dropped.
	 *
	 * @param  string|array  $index
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropUnique($index)
	{
		return $this->dropIndexCommand('dropUnique', 'unique', $index);
	}

	/**
	 * Indicate that the given index should be dropped.
	 *
	 * @param  string|array  $index
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropIndex($index)
	{
		return $this->dropIndexCommand('dropIndex', 'index', $index);
	}

	/**
	 * Indicate that the given foreign key should be dropped.
	 *
	 * @param  string  $index
	 * @return \Illuminate\Support\Fluent
	 */
	public function dropForeign($index)
	{
		return $this->dropIndexCommand('dropForeign', 'foreign', $index);
	}

	/**
	 * Indicate that the timestamp columns should be dropped.
	 *
	 * @return void
	 */
	public function dropTimestamps()
	{
		$this->dropColumn('created_at', 'updated_at');
	}

	/**
	* Indicate that the soft delete column should be dropped.
	*
	* @return void
	*/
	public function dropSoftDeletes()
	{
		$this->dropColumn('deleted_at');
	}

	/**
	 * Rename the table to a given name.
	 *
	 * @param  string  $to
	 * @return \Illuminate\Support\Fluent
	 */
	public function rename($to)
	{
		return $this->addCommand('rename', compact('to'));
	}

	/**
	 * Specify the primary key(s) for the table.
	 *
	 * @param  string|array  $columns
	 * @param  string  $name
	 * @return \Illuminate\Support\Fluent
	 */
	public function primary($columns, $name = null)
	{
		return $this->indexCommand('primary', $columns, $name);
	}

	/**
	 * Specify a unique index for the table.
	 *
	 * @param  string|array  $columns
	 * @param  string  $name
	 * @return \Illuminate\Support\Fluent
	 */
	public function unique($columns, $name = null)
	{
		return $this->indexCommand('unique', $columns, $name);
	}

	/**
	 * Specify an index for the table.
	 *
	 * @param  string|array  $columns
	 * @param  string  $name
	 * @return \Illuminate\Support\Fluent
	 */
	public function index($columns, $name = null)
	{
		return $this->indexCommand('index', $columns, $name);
	}

	/**
	 * Specify a foreign key for the table.
	 *
	 * @param  string|array  $columns
	 * @param  string  $name
	 * @return \Illuminate\Support\Fluent
	 */
	public function foreign($columns, $name = null)
	{
		return $this->indexCommand('foreign', $columns, $name);
	}

	/**
	 * Create a new auto-incrementing integer column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function increments($column)
	{
		return $this->unsignedInteger($column, true);
	}

	/**
	 * Create a new auto-incrementing big integer column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function bigIncrements($column)
	{
		return $this->unsignedBigInteger($column, true);
	}

	/**
	 * Create a new char column on the table.
	 *
	 * @param  string  $column
	 * @param  int  $length
	 * @return \Illuminate\Support\Fluent
	 */
	public function char($column, $length = 255)
	{
		return $this->addColumn('char', $column, compact('length'));
	}

	/**
	 * Create a new string column on the table.
	 *
	 * @param  string  $column
	 * @param  int  $length
	 * @return \Illuminate\Support\Fluent
	 */
	public function string($column, $length = 255)
	{
		return $this->addColumn('string', $column, compact('length'));
	}

	/**
	 * Create a new text column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function text($column)
	{
		return $this->addColumn('text', $column);
	}

	/**
	 * Create a new medium text column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function mediumText($column)
	{
		return $this->addColumn('mediumText', $column);
	}

	/**
	 * Create a new long text column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function longText($column)
	{
		return $this->addColumn('longText', $column);
	}

	/**
	 * Create a new integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @param  bool  $unsigned
	 * @return \Illuminate\Support\Fluent
	 */
	public function integer($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
	}

	/**
	 * Create a new big integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @param  bool  $unsigned
	 * @return \Illuminate\Support\Fluent
	 */
	public function bigInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('bigInteger', $column, compact('autoIncrement', 'unsigned'));
	}

	/**
	 * Create a new medium integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @param  bool  $unsigned
	 * @return \Illuminate\Support\Fluent
	 */
	public function mediumInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('mediumInteger', $column, compact('autoIncrement', 'unsigned'));
	}

	/**
	 * Create a new tiny integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @param  bool  $unsigned
	 * @return \Illuminate\Support\Fluent
	 */
	public function tinyInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
	}

	/**
	 * Create a new small integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @param  bool  $unsigned
	 * @return \Illuminate\Support\Fluent
	 */
	public function smallInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('smallInteger', $column, compact('autoIncrement', 'unsigned'));
	}

	/**
	 * Create a new unsigned integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @return \Illuminate\Support\Fluent
	 */
	public function unsignedInteger($column, $autoIncrement = false)
	{
		return $this->integer($column, $autoIncrement, true);
	}

	/**
	 * Create a new unsigned big integer column on the table.
	 *
	 * @param  string  $column
	 * @param  bool  $autoIncrement
	 * @return \Illuminate\Support\Fluent
	 */
	public function unsignedBigInteger($column, $autoIncrement = false)
	{
		return $this->bigInteger($column, $autoIncrement, true);
	}

	/**
	 * Create a new float column on the table.
	 *
	 * @param  string  $column
	 * @param  int     $total
	 * @param  int     $places
	 * @return \Illuminate\Support\Fluent
	 */
	public function float($column, $total = 8, $places = 2)
	{
		return $this->addColumn('float', $column, compact('total', 'places'));
	}

	/**
	 * Create a new double column on the table.
	 *
	 * @param  string   $column
	 * @param  int|null	$total
	 * @param  int|null $places
	 * @return \Illuminate\Support\Fluent
	 *
	 */
	public function double($column, $total = null, $places = null)
	{
		return $this->addColumn('double', $column, compact('total', 'places'));
	}

	/**
	 * Create a new decimal column on the table.
	 *
	 * @param  string  $column
	 * @param  int     $total
	 * @param  int     $places
	 * @return \Illuminate\Support\Fluent
	 */
	public function decimal($column, $total = 8, $places = 2)
	{
		return $this->addColumn('decimal', $column, compact('total', 'places'));
	}

	/**
	 * Create a new boolean column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function boolean($column)
	{
		return $this->addColumn('boolean', $column);
	}

	/**
	 * Create a new enum column on the table.
	 *
	 * @param  string  $column
	 * @param  array   $allowed
	 * @return \Illuminate\Support\Fluent
	 */
	public function enum($column, array $allowed)
	{
		return $this->addColumn('enum', $column, compact('allowed'));
	}

	/**
	 * Create a new date column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function date($column)
	{
		return $this->addColumn('date', $column);
	}

	/**
	 * Create a new date-time column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function dateTime($column)
	{
		return $this->addColumn('dateTime', $column);
	}

	/**
	 * Create a new time column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function time($column)
	{
		return $this->addColumn('time', $column);
	}

	/**
	 * Create a new timestamp column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function timestamp($column)
	{
		return $this->addColumn('timestamp', $column);
	}

	/**
	 * Add nullable creation and update timestamps to the table.
	 *
	 * @return void
	 */
	public function nullableTimestamps()
	{
		$this->timestamp('created_at')->nullable();

		$this->timestamp('updated_at')->nullable();
	}

	/**
	 * Add creation and update timestamps to the table.
	 *
	 * @return void
	 */
	public function timestamps()
	{
		$this->timestamp('created_at');

		$this->timestamp('updated_at');
	}

	/**
	 * Add a "deleted at" timestamp for the table.
	 *
	 * @return void
	 */
	public function softDeletes()
	{
		$this->timestamp('deleted_at')->nullable();
	}

	/**
	 * Create a new binary column on the table.
	 *
	 * @param  string  $column
	 * @return \Illuminate\Support\Fluent
	 */
	public function binary($column)
	{
		return $this->addColumn('binary', $column);
	}

	/**
	 * Add the proper columns for a polymorphic table.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function morphs($name)
	{
		$this->unsignedInteger("{$name}_id");

		$this->string("{$name}_type");
	}

	/**
	 * Create a new drop index command on the blueprint.
	 *
	 * @param  string  $command
	 * @param  string  $type
	 * @param  string|array  $index
	 * @return \Illuminate\Support\Fluent
	 */
	protected function dropIndexCommand($command, $type, $index)
	{
		$columns = array();

		// If the given "index" is actually an array of columns, the developer means
		// to drop an index merely by specifying the columns involved without the
		// conventional name, so we will built the index name from the columns.
		if (is_array($index))
		{
			$columns = $index;

			$index = $this->createIndexName($type, $columns);
		}

		return $this->indexCommand($command, $columns, $index);
	}

	/**
	 * Add a new index command to the blueprint.
	 *
	 * @param  string        $type
	 * @param  string|array  $columns
	 * @param  string        $index
	 * @return \Illuminate\Support\Fluent
	 */
	protected function indexCommand($type, $columns, $index)
	{
		$columns = (array) $columns;

		// If no name was specified for this index, we will create one using a basic
		// convention of the table name, followed by the columns, followed by an
		// index type, such as primary or index, which makes the index unique.
		if (is_null($index))
		{
			$index = $this->createIndexName($type, $columns);
		}

		return $this->addCommand($type, compact('index', 'columns'));
	}

	/**
	 * Create a default index name for the table.
	 *
	 * @param  string  $type
	 * @param  array   $columns
	 * @return string
	 */
	protected function createIndexName($type, array $columns)
	{
		$index = strtolower($this->table.'_'.implode('_', $columns).'_'.$type);

		return str_replace(array('-', '.'), '_', $index);
	}

	/**
	 * Add a new column to the blueprint.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return \Illuminate\Support\Fluent
	 */
	protected function addColumn($type, $name, array $parameters = array())
	{
		$attributes = array_merge(compact('type', 'name'), $parameters);

		$this->columns[] = $column = new Fluent($attributes);

		return $column;
	}

	/**
	 * Remove a column from the schema blueprint.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Schema\Blueprint
	 */
	public function removeColumn($name)
	{
		$this->columns = array_values(array_filter($this->columns, function($c) use ($name)
		{
			return $c['attributes']['name'] != $name;
		}));

		return $this;
	}

	/**
	 * Add a new command to the blueprint.
	 *
	 * @param  string  $name
	 * @param  array  $parameters
	 * @return \Illuminate\Support\Fluent
	 */
	protected function addCommand($name, array $parameters = array())
	{
		$this->commands[] = $command = $this->createCommand($name, $parameters);

		return $command;
	}

	/**
	 * Create a new Fluent command.
	 *
	 * @param  string  $name
	 * @param  array   $parameters
	 * @return \Illuminate\Support\Fluent
	 */
	protected function createCommand($name, array $parameters = array())
	{
		return new Fluent(array_merge(compact('name'), $parameters));
	}

	/**
	 * Get the table the blueprint describes.
	 *
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Get the columns that should be added.
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * Get the commands on the blueprint.
	 *
	 * @return array
	 */
	public function getCommands()
	{
		return $this->commands;
	}

}
