<?php

namespace Illuminate\Database\Schema;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;

use function Illuminate\Support\enum_value;

class Blueprint
{
    use Macroable;

    /**
     * The database connection instance.
     */
    protected Connection $connection;

    /**
     * The schema grammar instance.
     */
    protected Grammar $grammar;

    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected $table;

    /**
     * The columns that should be added to the table.
     *
     * @var \Illuminate\Database\Schema\ColumnDefinition[]
     */
    protected $columns = [];

    /**
     * The commands that should be run for the table.
     *
     * @var \Illuminate\Support\Fluent[]
     */
    protected $commands = [];

    /**
     * The storage engine that should be used for the table.
     *
     * @var string
     */
    public $engine;

    /**
     * The default character set that should be used for the table.
     *
     * @var string
     */
    public $charset;

    /**
     * The collation that should be used for the table.
     *
     * @var string
     */
    public $collation;

    /**
     * Whether to make the table temporary.
     *
     * @var bool
     */
    public $temporary = false;

    /**
     * The column to add new columns after.
     *
     * @var string
     */
    public $after;

    /**
     * The blueprint state instance.
     *
     * @var \Illuminate\Database\Schema\BlueprintState|null
     */
    protected $state;

    /**
     * Create a new schema blueprint.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  (\Closure(self): void)|null  $callback
     */
    public function __construct(Connection $connection, $table, ?Closure $callback = null)
    {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();
        $this->table = $table;

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * Execute the blueprint against the database.
     *
     * @return void
     */
    public function build()
    {
        foreach ($this->toSql() as $statement) {
            $this->connection->statement($statement);
        }
    }

    /**
     * Get the raw SQL statements for the blueprint.
     *
     * @return array
     */
    public function toSql()
    {
        $this->addImpliedCommands();

        $statements = [];

        // Each type of command has a corresponding compiler function on the schema
        // grammar which is used to build the necessary SQL statements to build
        // the blueprint element, so we'll just call that compilers function.
        $this->ensureCommandsAreValid();

        foreach ($this->commands as $command) {
            if ($command->shouldBeSkipped) {
                continue;
            }

            $method = 'compile'.ucfirst($command->name);

            if (method_exists($this->grammar, $method) || $this->grammar::hasMacro($method)) {
                if ($this->hasState()) {
                    $this->state->update($command);
                }

                if (! is_null($sql = $this->grammar->$method($this, $command))) {
                    $statements = array_merge($statements, (array) $sql);
                }
            }
        }

        return $statements;
    }

    /**
     * Ensure the commands on the blueprint are valid for the connection type.
     *
     * @return void
     *
     * @throws \BadMethodCallException
     */
    protected function ensureCommandsAreValid()
    {
        //
    }

    /**
     * Get all of the commands matching the given names.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @param  array  $names
     * @return \Illuminate\Support\Collection
     */
    protected function commandsNamed(array $names)
    {
        return (new Collection($this->commands))
            ->filter(fn ($command) => in_array($command->name, $names));
    }

    /**
     * Add the commands that are implied by the blueprint's state.
     *
     * @return void
     */
    protected function addImpliedCommands()
    {
        $this->addFluentIndexes();
        $this->addFluentCommands();

        if (! $this->creating()) {
            $this->commands = array_map(
                fn ($command) => $command instanceof ColumnDefinition
                    ? $this->createCommand($command->change ? 'change' : 'add', ['column' => $command])
                    : $command,
                $this->commands
            );

            $this->addAlterCommands();
        }
    }

    /**
     * Add the index commands fluently specified on columns.
     *
     * @return void
     */
    protected function addFluentIndexes()
    {
        foreach ($this->columns as $column) {
            foreach (['primary', 'unique', 'index', 'fulltext', 'fullText', 'spatialIndex', 'vectorIndex'] as $index) {
                // If the column is supposed to be changed to an auto increment column and
                // the specified index is primary, there is no need to add a command on
                // MySQL, as it will be handled during the column definition instead.
                if ($index === 'primary' && $column->autoIncrement && $column->change && $this->grammar instanceof MySqlGrammar) {
                    continue 2;
                }

                // If the index has been specified on the given column, but is simply equal
                // to "true" (boolean), no name has been specified for this index so the
                // index method can be called without a name and it will generate one.
                if ($column->{$index} === true) {
                    $indexMethod = $index === 'index' && $column->type === 'vector'
                        ? 'vectorIndex'
                        : $index;

                    $this->{$indexMethod}($column->name);
                    $column->{$index} = null;

                    continue 2;
                }

                // If the index has been specified on the given column, but it equals false
                // and the column is supposed to be changed, we will call the drop index
                // method with an array of column to drop it by its conventional name.
                elseif ($column->{$index} === false && $column->change) {
                    $this->{'drop'.ucfirst($index)}([$column->name]);
                    $column->{$index} = null;

                    continue 2;
                }

                // If the index has been specified on the given column, and it has a string
                // value, we'll go ahead and call the index method and pass the name for
                // the index since the developer specified the explicit name for this.
                elseif (isset($column->{$index})) {
                    $indexMethod = $index === 'index' && $column->type === 'vector'
                        ? 'vectorIndex'
                        : $index;

                    $this->{$indexMethod}($column->name, $column->{$index});
                    $column->{$index} = null;

                    continue 2;
                }
            }
        }
    }

    /**
     * Add the fluent commands specified on any columns.
     *
     * @return void
     */
    public function addFluentCommands()
    {
        foreach ($this->columns as $column) {
            foreach ($this->grammar->getFluentCommands() as $commandName) {
                $this->addCommand($commandName, compact('column'));
            }
        }
    }

    /**
     * Add the alter commands if whenever needed.
     *
     * @return void
     */
    public function addAlterCommands()
    {
        if (! $this->grammar instanceof SQLiteGrammar) {
            return;
        }

        $alterCommands = $this->grammar->getAlterCommands();

        [$commands, $lastCommandWasAlter, $hasAlterCommand] = [
            [], false, false,
        ];

        foreach ($this->commands as $command) {
            if (in_array($command->name, $alterCommands)) {
                $hasAlterCommand = true;
                $lastCommandWasAlter = true;
            } elseif ($lastCommandWasAlter) {
                $commands[] = $this->createCommand('alter');
                $lastCommandWasAlter = false;
            }

            $commands[] = $command;
        }

        if ($lastCommandWasAlter) {
            $commands[] = $this->createCommand('alter');
        }

        if ($hasAlterCommand) {
            $this->state = new BlueprintState($this, $this->connection);
        }

        $this->commands = $commands;
    }

    /**
     * Determine if the blueprint has a create command.
     *
     * @return bool
     */
    public function creating()
    {
        return (new Collection($this->commands))
            ->contains(fn ($command) => ! $command instanceof ColumnDefinition && $command->name === 'create');
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
     * Specify the storage engine that should be used for the table.
     *
     * @param  string  $engine
     * @return void
     */
    public function engine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * Specify that the InnoDB storage engine should be used for the table (MySQL only).
     *
     * @return void
     */
    public function innoDb()
    {
        $this->engine('InnoDB');
    }

    /**
     * Specify the character set that should be used for the table.
     *
     * @param  string  $charset
     * @return void
     */
    public function charset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Specify the collation that should be used for the table.
     *
     * @param  string  $collation
     * @return void
     */
    public function collation($collation)
    {
        $this->collation = $collation;
    }

    /**
     * Indicate that the table needs to be temporary.
     *
     * @return void
     */
    public function temporary()
    {
        $this->temporary = true;
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
     * @param  mixed  $columns
     * @return \Illuminate\Support\Fluent
     */
    public function dropColumn($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

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
     * @param  string|array|null  $index
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
     * Indicate that the given fulltext index should be dropped.
     *
     * @param  string|array  $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropFullText($index)
    {
        return $this->dropIndexCommand('dropFullText', 'fulltext', $index);
    }

    /**
     * Indicate that the given spatial index should be dropped.
     *
     * @param  string|array  $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropSpatialIndex($index)
    {
        return $this->dropIndexCommand('dropSpatialIndex', 'spatialIndex', $index);
    }

    /**
     * Indicate that the given foreign key should be dropped.
     *
     * @param  string|array  $index
     * @return \Illuminate\Support\Fluent
     */
    public function dropForeign($index)
    {
        return $this->dropIndexCommand('dropForeign', 'foreign', $index);
    }

    /**
     * Indicate that the given column and foreign key should be dropped.
     *
     * @param  string  $column
     * @return \Illuminate\Support\Fluent
     */
    public function dropConstrainedForeignId($column)
    {
        $this->dropForeign([$column]);

        return $this->dropColumn($column);
    }

    /**
     * Indicate that the given foreign key should be dropped.
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string|null  $column
     * @return \Illuminate\Support\Fluent
     */
    public function dropForeignIdFor($model, $column = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }

        return $this->dropColumn($column ?: $model->getForeignKey());
    }

    /**
     * Indicate that the given foreign key should be dropped.
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string|null  $column
     * @return \Illuminate\Support\Fluent
     */
    public function dropConstrainedForeignIdFor($model, $column = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }

        return $this->dropConstrainedForeignId($column ?: $model->getForeignKey());
    }

    /**
     * Indicate that the given indexes should be renamed.
     *
     * @param  string  $from
     * @param  string  $to
     * @return \Illuminate\Support\Fluent
     */
    public function renameIndex($from, $to)
    {
        return $this->addCommand('renameIndex', compact('from', 'to'));
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
     * Indicate that the timestamp columns should be dropped.
     *
     * @return void
     */
    public function dropTimestampsTz()
    {
        $this->dropTimestamps();
    }

    /**
     * Indicate that the soft delete column should be dropped.
     *
     * @param  string  $column
     * @return void
     */
    public function dropSoftDeletes($column = 'deleted_at')
    {
        $this->dropColumn($column);
    }

    /**
     * Indicate that the soft delete column should be dropped.
     *
     * @param  string  $column
     * @return void
     */
    public function dropSoftDeletesTz($column = 'deleted_at')
    {
        $this->dropSoftDeletes($column);
    }

    /**
     * Indicate that the remember token column should be dropped.
     *
     * @return void
     */
    public function dropRememberToken()
    {
        $this->dropColumn('remember_token');
    }

    /**
     * Indicate that the polymorphic columns should be dropped.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function dropMorphs($name, $indexName = null)
    {
        $this->dropIndex($indexName ?: $this->createIndexName('index', ["{$name}_type", "{$name}_id"]));

        $this->dropColumn("{$name}_type", "{$name}_id");
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
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function primary($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('primary', $columns, $name, $algorithm);
    }

    /**
     * Specify a unique index for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function unique($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('unique', $columns, $name, $algorithm);
    }

    /**
     * Specify an index for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function index($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('index', $columns, $name, $algorithm);
    }

    /**
     * Specify a fulltext index for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function fullText($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('fulltext', $columns, $name, $algorithm);
    }

    /**
     * Specify a spatial index for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @param  string|null  $operatorClass
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function spatialIndex($columns, $name = null, $operatorClass = null)
    {
        return $this->indexCommand('spatialIndex', $columns, $name, null, $operatorClass);
    }

    /**
     * Specify a vector index for the table.
     *
     * @param  string  $column
     * @param  string|null  $name
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function vectorIndex($column, $name = null)
    {
        return $this->indexCommand('vectorIndex', $column, $name, 'hnsw', 'vector_cosine_ops');
    }

    /**
     * Specify a raw index for the table.
     *
     * @param  string  $expression
     * @param  string  $name
     * @return \Illuminate\Database\Schema\IndexDefinition
     */
    public function rawIndex($expression, $name)
    {
        return $this->index([new Expression($expression)], $name);
    }

    /**
     * Specify a foreign key for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @return \Illuminate\Database\Schema\ForeignKeyDefinition
     */
    public function foreign($columns, $name = null)
    {
        $command = new ForeignKeyDefinition(
            $this->indexCommand('foreign', $columns, $name)->getAttributes()
        );

        $this->commands[count($this->commands) - 1] = $command;

        return $command;
    }

    /**
     * Create a new auto-incrementing big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function id($column = 'id')
    {
        return $this->bigIncrements($column);
    }

    /**
     * Create a new auto-incrementing integer column on the table (4-byte, 0 to 4,294,967,295).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function increments($column)
    {
        return $this->unsignedInteger($column, true);
    }

    /**
     * Create a new auto-incrementing integer column on the table (4-byte, 0 to 4,294,967,295).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function integerIncrements($column)
    {
        return $this->unsignedInteger($column, true);
    }

    /**
     * Create a new auto-incrementing tiny integer column on the table (1-byte, 0 to 255).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function tinyIncrements($column)
    {
        return $this->unsignedTinyInteger($column, true);
    }

    /**
     * Create a new auto-incrementing small integer column on the table (2-byte, 0 to 65,535).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function smallIncrements($column)
    {
        return $this->unsignedSmallInteger($column, true);
    }

    /**
     * Create a new auto-incrementing medium integer column on the table (3-byte, 0 to 16,777,215).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function mediumIncrements($column)
    {
        return $this->unsignedMediumInteger($column, true);
    }

    /**
     * Create a new auto-incrementing big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function bigIncrements($column)
    {
        return $this->unsignedBigInteger($column, true);
    }

    /**
     * Create a new char column on the table.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function char($column, $length = null)
    {
        $length = ! is_null($length) ? $length : Builder::$defaultStringLength;

        return $this->addColumn('char', $column, compact('length'));
    }

    /**
     * Create a new string column on the table.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function string($column, $length = null)
    {
        $length = $length ?: Builder::$defaultStringLength;

        return $this->addColumn('string', $column, compact('length'));
    }

    /**
     * Create a new tiny text column on the table (up to 255 characters).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function tinyText($column)
    {
        return $this->addColumn('tinyText', $column);
    }

    /**
     * Create a new text column on the table (up to 65,535 characters / ~64 KB).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function text($column)
    {
        return $this->addColumn('text', $column);
    }

    /**
     * Create a new medium text column on the table (up to 16,777,215 characters / ~16 MB).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function mediumText($column)
    {
        return $this->addColumn('mediumText', $column);
    }

    /**
     * Create a new long text column on the table (up to 4,294,967,295 characters / ~4 GB).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function longText($column)
    {
        return $this->addColumn('longText', $column);
    }

    /**
     * Create a new integer (4-byte) column on the table.
     * Range: -2,147,483,648 to 2,147,483,647 (signed) or 0 to 4,294,967,295 (unsigned).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @param  bool  $unsigned
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function integer($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new tiny integer (1-byte) column on the table.
     * Range: -128 to 127 (signed) or 0 to 255 (unsigned).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @param  bool  $unsigned
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function tinyInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new small integer (2-byte) column on the table.
     * Range: -32,768 to 32,767 (signed) or 0 to 65,535 (unsigned).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @param  bool  $unsigned
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function smallInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('smallInteger', $column, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new medium integer (3-byte) column on the table.
     * Range: -8,388,608 to 8,388,607 (signed) or 0 to 16,777,215 (unsigned).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @param  bool  $unsigned
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function mediumInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('mediumInteger', $column, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new big integer (8-byte) column on the table.
     * Range: -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807 (signed) or 0 to 18,446,744,073,709,551,615 (unsigned).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @param  bool  $unsigned
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function bigInteger($column, $autoIncrement = false, $unsigned = false)
    {
        return $this->addColumn('bigInteger', $column, compact('autoIncrement', 'unsigned'));
    }

    /**
     * Create a new unsigned integer column on the table (4-byte, 0 to 4,294,967,295).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function unsignedInteger($column, $autoIncrement = false)
    {
        return $this->integer($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned tiny integer column on the table (1-byte, 0 to 255).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function unsignedTinyInteger($column, $autoIncrement = false)
    {
        return $this->tinyInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned small integer column on the table (2-byte, 0 to 65,535).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function unsignedSmallInteger($column, $autoIncrement = false)
    {
        return $this->smallInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned medium integer column on the table (3-byte, 0 to 16,777,215).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function unsignedMediumInteger($column, $autoIncrement = false)
    {
        return $this->mediumInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
     *
     * @param  string  $column
     * @param  bool  $autoIncrement
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function unsignedBigInteger($column, $autoIncrement = false)
    {
        return $this->bigInteger($column, $autoIncrement, true);
    }

    /**
     * Create a new unsigned big integer column on the table (8-byte, 0 to 18,446,744,073,709,551,615).
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ForeignIdColumnDefinition
     */
    public function foreignId($column)
    {
        return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
            'type' => 'bigInteger',
            'name' => $column,
            'autoIncrement' => false,
            'unsigned' => true,
        ]));
    }

    /**
     * Create a foreign ID column for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string|null  $column
     * @return \Illuminate\Database\Schema\ForeignIdColumnDefinition
     */
    public function foreignIdFor($model, $column = null)
    {
        if (is_string($model)) {
            $model = new $model;
        }

        $column = $column ?: $model->getForeignKey();

        if ($model->getKeyType() === 'int') {
            return $this->foreignId($column)
                ->table($model->getTable())
                ->referencesModelColumn($model->getKeyName());
        }

        $modelTraits = class_uses_recursive($model);

        if (in_array(HasUlids::class, $modelTraits, true)) {
            return $this->foreignUlid($column, 26)
                ->table($model->getTable())
                ->referencesModelColumn($model->getKeyName());
        }

        return $this->foreignUuid($column)
            ->table($model->getTable())
            ->referencesModelColumn($model->getKeyName());
    }

    /**
     * Create a new float column on the table.
     *
     * @param  string  $column
     * @param  int  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function float($column, $precision = 53)
    {
        return $this->addColumn('float', $column, compact('precision'));
    }

    /**
     * Create a new double column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function double($column)
    {
        return $this->addColumn('double', $column);
    }

    /**
     * Create a new decimal column on the table.
     *
     * @param  string  $column
     * @param  int  $total
     * @param  int  $places
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function decimal($column, $total = 8, $places = 2)
    {
        return $this->addColumn('decimal', $column, compact('total', 'places'));
    }

    /**
     * Create a new boolean column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function boolean($column)
    {
        return $this->addColumn('boolean', $column);
    }

    /**
     * Create a new enum column on the table.
     *
     * @param  string  $column
     * @param  array  $allowed
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function enum($column, array $allowed)
    {
        $allowed = array_map(fn ($value) => enum_value($value), $allowed);

        return $this->addColumn('enum', $column, compact('allowed'));
    }

    /**
     * Create a new set column on the table.
     *
     * @param  string  $column
     * @param  array  $allowed
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function set($column, array $allowed)
    {
        return $this->addColumn('set', $column, compact('allowed'));
    }

    /**
     * Create a new json column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function json($column)
    {
        return $this->addColumn('json', $column);
    }

    /**
     * Create a new jsonb column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function jsonb($column)
    {
        return $this->addColumn('jsonb', $column);
    }

    /**
     * Create a new date column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function date($column)
    {
        return $this->addColumn('date', $column);
    }

    /**
     * Create a new date-time column on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function dateTime($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('dateTime', $column, compact('precision'));
    }

    /**
     * Create a new date-time column (with time zone) on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function dateTimeTz($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('dateTimeTz', $column, compact('precision'));
    }

    /**
     * Create a new time column on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function time($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('time', $column, compact('precision'));
    }

    /**
     * Create a new time column (with time zone) on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function timeTz($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('timeTz', $column, compact('precision'));
    }

    /**
     * Create a new timestamp column on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function timestamp($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('timestamp', $column, compact('precision'));
    }

    /**
     * Create a new timestamp (with time zone) column on the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function timestampTz($column, $precision = null)
    {
        $precision ??= $this->defaultTimePrecision();

        return $this->addColumn('timestampTz', $column, compact('precision'));
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * @param  int|null  $precision
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Schema\ColumnDefinition>
     */
    public function timestamps($precision = null)
    {
        return new Collection([
            $this->timestamp('created_at', $precision)->nullable(),
            $this->timestamp('updated_at', $precision)->nullable(),
        ]);
    }

    /**
     * Add nullable creation and update timestamps to the table.
     *
     * Alias for self::timestamps().
     *
     * @param  int|null  $precision
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Schema\ColumnDefinition>
     */
    public function nullableTimestamps($precision = null)
    {
        return $this->timestamps($precision);
    }

    /**
     * Add nullable creation and update timestampTz columns to the table.
     *
     * @param  int|null  $precision
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Schema\ColumnDefinition>
     */
    public function timestampsTz($precision = null)
    {
        return new Collection([
            $this->timestampTz('created_at', $precision)->nullable(),
            $this->timestampTz('updated_at', $precision)->nullable(),
        ]);
    }

    /**
     * Add nullable creation and update timestampTz columns to the table.
     *
     * Alias for self::timestampsTz().
     *
     * @param  int|null  $precision
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Schema\ColumnDefinition>
     */
    public function nullableTimestampsTz($precision = null)
    {
        return $this->timestampsTz($precision);
    }

    /**
     * Add creation and update datetime columns to the table.
     *
     * @param  int|null  $precision
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Schema\ColumnDefinition>
     */
    public function datetimes($precision = null)
    {
        return new Collection([
            $this->datetime('created_at', $precision)->nullable(),
            $this->datetime('updated_at', $precision)->nullable(),
        ]);
    }

    /**
     * Add a "deleted at" timestamp for the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletes($column = 'deleted_at', $precision = null)
    {
        return $this->timestamp($column, $precision)->nullable();
    }

    /**
     * Add a "deleted at" timestampTz for the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletesTz($column = 'deleted_at', $precision = null)
    {
        return $this->timestampTz($column, $precision)->nullable();
    }

    /**
     * Add a "deleted at" datetime column to the table.
     *
     * @param  string  $column
     * @param  int|null  $precision
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function softDeletesDatetime($column = 'deleted_at', $precision = null)
    {
        return $this->datetime($column, $precision)->nullable();
    }

    /**
     * Create a new year column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function year($column)
    {
        return $this->addColumn('year', $column);
    }

    /**
     * Create a new binary column on the table.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @param  bool  $fixed
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function binary($column, $length = null, $fixed = false)
    {
        return $this->addColumn('binary', $column, compact('length', 'fixed'));
    }

    /**
     * Create a new UUID column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function uuid($column = 'uuid')
    {
        return $this->addColumn('uuid', $column);
    }

    /**
     * Create a new UUID column on the table with a foreign key constraint.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ForeignIdColumnDefinition
     */
    public function foreignUuid($column)
    {
        return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
            'type' => 'uuid',
            'name' => $column,
        ]));
    }

    /**
     * Create a new ULID column on the table.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function ulid($column = 'ulid', $length = 26)
    {
        return $this->char($column, $length);
    }

    /**
     * Create a new ULID column on the table with a foreign key constraint.
     *
     * @param  string  $column
     * @param  int|null  $length
     * @return \Illuminate\Database\Schema\ForeignIdColumnDefinition
     */
    public function foreignUlid($column, $length = 26)
    {
        return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
            'type' => 'char',
            'name' => $column,
            'length' => $length,
        ]));
    }

    /**
     * Create a new IP address column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function ipAddress($column = 'ip_address')
    {
        return $this->addColumn('ipAddress', $column);
    }

    /**
     * Create a new MAC address column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function macAddress($column = 'mac_address')
    {
        return $this->addColumn('macAddress', $column);
    }

    /**
     * Create a new geometry column on the table.
     *
     * @param  string  $column
     * @param  string|null  $subtype
     * @param  int  $srid
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function geometry($column, $subtype = null, $srid = 0)
    {
        return $this->addColumn('geometry', $column, compact('subtype', 'srid'));
    }

    /**
     * Create a new geography column on the table.
     *
     * @param  string  $column
     * @param  string|null  $subtype
     * @param  int  $srid
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function geography($column, $subtype = null, $srid = 4326)
    {
        return $this->addColumn('geography', $column, compact('subtype', 'srid'));
    }

    /**
     * Create a new generated, computed column on the table.
     *
     * @param  string  $column
     * @param  string  $expression
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function computed($column, $expression)
    {
        return $this->addColumn('computed', $column, compact('expression'));
    }

    /**
     * Create a new vector column on the table.
     *
     * @param  string  $column
     * @param  int|null  $dimensions
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function vector($column, $dimensions = null)
    {
        $options = $dimensions ? compact('dimensions') : [];

        return $this->addColumn('vector', $column, $options);
    }

    /**
     * Create a new tsvector column on the table.
     *
     * @param  string  $column
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function tsvector($column)
    {
        return $this->addColumn('tsvector', $column);
    }

    /**
     * Add the proper columns for a polymorphic table.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function morphs($name, $indexName = null, $after = null)
    {
        if (Builder::$defaultMorphKeyType === 'uuid') {
            $this->uuidMorphs($name, $indexName, $after);
        } elseif (Builder::$defaultMorphKeyType === 'ulid') {
            $this->ulidMorphs($name, $indexName, $after);
        } else {
            $this->numericMorphs($name, $indexName, $after);
        }
    }

    /**
     * Add nullable columns for a polymorphic table.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function nullableMorphs($name, $indexName = null, $after = null)
    {
        if (Builder::$defaultMorphKeyType === 'uuid') {
            $this->nullableUuidMorphs($name, $indexName, $after);
        } elseif (Builder::$defaultMorphKeyType === 'ulid') {
            $this->nullableUlidMorphs($name, $indexName, $after);
        } else {
            $this->nullableNumericMorphs($name, $indexName, $after);
        }
    }

    /**
     * Add the proper columns for a polymorphic table using numeric IDs (incremental).
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function numericMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->after($after);

        $this->unsignedBigInteger("{$name}_id")
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic table using numeric IDs (incremental).
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function nullableNumericMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->nullable()
            ->after($after);

        $this->unsignedBigInteger("{$name}_id")
            ->nullable()
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add the proper columns for a polymorphic table using UUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function uuidMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->after($after);

        $this->uuid("{$name}_id")
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic table using UUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function nullableUuidMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->nullable()
            ->after($after);

        $this->uuid("{$name}_id")
            ->nullable()
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add the proper columns for a polymorphic table using ULIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function ulidMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->after($after);

        $this->ulid("{$name}_id")
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add nullable columns for a polymorphic table using ULIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @param  string|null  $after
     * @return void
     */
    public function nullableUlidMorphs($name, $indexName = null, $after = null)
    {
        $this->string("{$name}_type")
            ->nullable()
            ->after($after);

        $this->ulid("{$name}_id")
            ->nullable()
            ->after(! is_null($after) ? "{$name}_type" : null);

        $this->index(["{$name}_type", "{$name}_id"], $indexName);
    }

    /**
     * Add the `remember_token` column to the table.
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function rememberToken()
    {
        return $this->string('remember_token', 100)->nullable();
    }

    /**
     * Create a new custom column on the table.
     *
     * @param  string  $column
     * @param  string  $definition
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function rawColumn($column, $definition)
    {
        return $this->addColumn('raw', $column, compact('definition'));
    }

    /**
     * Add a comment to the table.
     *
     * @param  string  $comment
     * @return \Illuminate\Support\Fluent
     */
    public function comment($comment)
    {
        return $this->addCommand('tableComment', compact('comment'));
    }

    /**
     * Create a new index command on the blueprint.
     *
     * @param  string  $type
     * @param  string|array  $columns
     * @param  string  $index
     * @param  string|null  $algorithm
     * @param  string|null  $operatorClass
     * @return \Illuminate\Support\Fluent
     */
    protected function indexCommand($type, $columns, $index, $algorithm = null, $operatorClass = null)
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        $index = $index ?: $this->createIndexName($type, $columns);

        return $this->addCommand(
            $type, compact('index', 'columns', 'algorithm', 'operatorClass')
        );
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
        $columns = [];

        // If the given "index" is actually an array of columns, the developer means
        // to drop an index merely by specifying the columns involved without the
        // conventional name, so we will build the index name from the columns.
        if (is_array($index)) {
            $index = $this->createIndexName($type, $columns = $index);
        }

        return $this->indexCommand($command, $columns, $index);
    }

    /**
     * Create a default index name for the table.
     *
     * @param  string  $type
     * @param  array  $columns
     * @return string
     */
    protected function createIndexName($type, array $columns)
    {
        $table = $this->table;

        if ($this->connection->getConfig('prefix_indexes')) {
            $table = str_contains($this->table, '.')
                ? substr_replace($this->table, '.'.$this->connection->getTablePrefix(), strrpos($this->table, '.'), 1)
                : $this->connection->getTablePrefix().$this->table;
        }

        $index = strtolower($table.'_'.implode('_', $columns).'_'.$type);

        return str_replace(['-', '.'], '_', $index);
    }

    /**
     * Add a new column to the blueprint.
     *
     * @param  string  $type
     * @param  string  $name
     * @param  array  $parameters
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function addColumn($type, $name, array $parameters = [])
    {
        return $this->addColumnDefinition(new ColumnDefinition(
            array_merge(compact('type', 'name'), $parameters)
        ));
    }

    /**
     * Add a new column definition to the blueprint.
     *
     * @param  \Illuminate\Database\Schema\ColumnDefinition  $definition
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    protected function addColumnDefinition($definition)
    {
        $this->columns[] = $definition;

        if (! $this->creating()) {
            $this->commands[] = $definition;
        }

        if ($this->after) {
            $definition->after($this->after);

            $this->after = $definition->name;
        }

        return $definition;
    }

    /**
     * Add the columns from the callback after the given column.
     *
     * @param  string  $column
     * @param  (\Closure(self): void)  $callback
     * @return void
     */
    public function after($column, Closure $callback)
    {
        $this->after = $column;

        $callback($this);

        $this->after = null;
    }

    /**
     * Remove a column from the schema blueprint.
     *
     * @param  string  $name
     * @return $this
     */
    public function removeColumn($name)
    {
        $this->columns = array_values(array_filter($this->columns, function ($c) use ($name) {
            return $c['name'] != $name;
        }));

        $this->commands = array_values(array_filter($this->commands, function ($c) use ($name) {
            return ! $c instanceof ColumnDefinition || $c['name'] != $name;
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
    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand($name, array $parameters = [])
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
     * Get the table prefix.
     *
     * @deprecated Use DB::getTablePrefix()
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->connection->getTablePrefix();
    }

    /**
     * Get the columns on the blueprint.
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get the commands on the blueprint.
     *
     * @return \Illuminate\Support\Fluent[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Determine if the blueprint has state.
     *
     * @return bool
     */
    private function hasState(): bool
    {
        return ! is_null($this->state);
    }

    /**
     * Get the state of the blueprint.
     *
     * @return \Illuminate\Database\Schema\BlueprintState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Get the columns on the blueprint that should be added.
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition[]
     */
    public function getAddedColumns()
    {
        return array_filter($this->columns, function ($column) {
            return ! $column->change;
        });
    }

    /**
     * Get the columns on the blueprint that should be changed.
     *
     * @deprecated Will be removed in a future Laravel version.
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition[]
     */
    public function getChangedColumns()
    {
        return array_filter($this->columns, function ($column) {
            return (bool) $column->change;
        });
    }

    /**
     * Get the default time precision.
     */
    protected function defaultTimePrecision(): ?int
    {
        return $this->connection->getSchemaBuilder()::$defaultTimePrecision;
    }
}
