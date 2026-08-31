<?php

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\SchemaLoaded;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\SQLiteDatabaseDoesNotExistException;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Str;
use PDOException;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'migrate')]
class MigrateCommand extends BaseCommand implements Isolatable
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--seeder= : The class name of the root seeder}
                {--step : Force the migrations to be run so they can be rolled back individually}
                {--graceful : Return a successful exit code even if an error occurs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new migration command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        parent::__construct();

        $this->migrator = $migrator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        try {
            $this->runMigrations();
        } catch (Throwable $e) {
            if ($this->option('graceful')) {
                $this->components->warn($e->getMessage());

                return 0;
            }

            throw $e;
        }

        return 0;
    }

    /**
     * Run the pending migrations.
     *
     * @return void
     */
    protected function runMigrations()
    {
        $this->migrator->usingConnection($this->option('database'), function () {
            $this->prepareDatabase();

            // Next, we will check to see if a path option has been defined. If it has
            // we will use the path relative to the root of this installation folder
            // so that migrations may be run for any path within the applications.
            $this->migrator->setOutput($this->output)
                ->run($this->getMigrationPaths(), [
                    'pretend' => $this->option('pretend'),
                    'step' => $this->option('step'),
                ]);

            // Finally, if the "seed" option has been given, we will re-run the database
            // seed task to re-populate the database, which is convenient when adding
            // a migration and a seed at the same time, as it is only this command.
            if ($this->option('seed') && ! $this->option('pretend')) {
                $this->call('db:seed', [
                    '--class' => $this->option('seeder') ?: 'Database\\Seeders\\DatabaseSeeder',
                    '--force' => true,
                ]);
            }
        });
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        if (! $this->repositoryExists()) {
            $this->components->info('Preparing database.');

            $this->components->task('Creating migration table', function () {
                return $this->callSilent('migrate:install', array_filter([
                    '--database' => $this->option('database'),
                ])) == 0;
            });

            $this->newLine();
        }

        if (! $this->migrator->hasRunAnyMigrations() && ! $this->option('pretend')) {
            $this->loadSchemaState();
        }
    }

    /**
     * Determine if the migrator repository exists.
     *
     * @return bool
     */
    protected function repositoryExists()
    {
        return retry(2, fn () => $this->migrator->repositoryExists(), 0, function ($e) {
            try {
                return $this->handleMissingDatabase($e->getPrevious());
            } catch (Throwable) {
                return false;
            }
        });
    }

    /**
     * Attempt to create the database if it is missing.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    protected function handleMissingDatabase(Throwable $e)
    {
        if ($e instanceof SQLiteDatabaseDoesNotExistException) {
            return $this->createMissingSqliteDatabase($e->path);
        }

        $connection = $this->migrator->resolveConnection($this->option('database'));

        if (! $e instanceof PDOException) {
            return false;
        }

        if (($e->getCode() === 1049 && in_array($connection->getDriverName(), ['mysql', 'mariadb'])) ||
            (($e->errorInfo[0] ?? null) == '08006' &&
              $connection->getDriverName() == 'pgsql' &&
              Str::contains($e->getMessage(), '"'.$connection->getDatabaseName().'"'))) {
            return $this->createMissingMySqlOrPgsqlDatabase($connection);
        }

        return false;
    }

    /**
     * Create a missing SQLite database.
     *
     * @param  string  $path
     * @return bool
     *
     * @throws \RuntimeException
     */
    protected function createMissingSqliteDatabase($path)
    {
        if ($this->option('force')) {
            return touch($path);
        }

        if ($this->option('no-interaction')) {
            return false;
        }

        $this->components->warn('The SQLite database configured for this application does not exist: '.$path);

        if (! confirm('Would you like to create it?', default: true)) {
            $this->components->info('Operation cancelled. No database was created.');

            throw new RuntimeException('Database was not created. Aborting migration.');
        }

        return touch($path);
    }

    /**
     * Create a missing MySQL or Postgres database.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return bool
     *
     * @throws \RuntimeException
     */
    protected function createMissingMySqlOrPgsqlDatabase($connection)
    {
        if ($this->laravel['config']->get("database.connections.{$connection->getName()}.database") !== $connection->getDatabaseName()) {
            return false;
        }

        if (! $this->option('force') && $this->option('no-interaction')) {
            return false;
        }

        if (! $this->option('force') && ! $this->option('no-interaction')) {
            $this->components->warn("The database '{$connection->getDatabaseName()}' does not exist on the '{$connection->getName()}' connection.");

            if (! confirm('Would you like to create it?', default: true)) {
                $this->components->info('Operation cancelled. No database was created.');

                throw new RuntimeException('Database was not created. Aborting migration.');
            }
        }
        try {
            $this->laravel['config']->set(
                "database.connections.{$connection->getName()}.database",
                match ($connection->getDriverName()) {
                    'mysql', 'mariadb' => null,
                    'pgsql' => 'postgres',
                },
            );

            $this->laravel['db']->purge();

            $freshConnection = $this->migrator->resolveConnection($this->option('database'));

            return tap($freshConnection->unprepared(
                match ($connection->getDriverName()) {
                    'mysql', 'mariadb' => "CREATE DATABASE IF NOT EXISTS `{$connection->getDatabaseName()}`",
                    'pgsql' => 'CREATE DATABASE "'.$connection->getDatabaseName().'"',
                }
            ), function () {
                $this->laravel['db']->purge();
            });
        } finally {
            $this->laravel['config']->set("database.connections.{$connection->getName()}.database", $connection->getDatabaseName());
        }
    }

    /**
     * Load the schema state to seed the initial database schema structure.
     *
     * @return void
     */
    protected function loadSchemaState()
    {
        $connection = $this->migrator->resolveConnection($this->option('database'));

        // First, we will make sure that the connection supports schema loading and that
        // the schema file exists before we proceed any further. If not, we will just
        // continue with the standard migration operation as normal without errors.
        if ($connection instanceof SqlServerConnection ||
            ! is_file($path = $this->schemaPath($connection))) {
            return;
        }

        $this->components->info('Loading stored database schemas.');

        $this->components->task($path, function () use ($connection, $path) {
            // Since the schema file will create the "migrations" table and reload it to its
            // proper state, we need to delete it here so we don't get an error that this
            // table already exists when the stored database schema file gets executed.
            $this->migrator->deleteRepository();

            $connection->getSchemaState()->handleOutputUsing(function ($type, $buffer) {
                $this->output->write($buffer);
            })->load($path);
        });

        $this->newLine();

        // Finally, we will fire an event that this schema has been loaded so developers
        // can perform any post schema load tasks that are necessary in listeners for
        // this event, which may seed the database tables with some necessary data.
        $this->dispatcher->dispatch(
            new SchemaLoaded($connection, $path)
        );
    }

    /**
     * Get the path to the stored schema for the given connection.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return string
     */
    protected function schemaPath($connection)
    {
        if ($this->option('schema-path')) {
            return $this->option('schema-path');
        }

        if (file_exists($path = database_path('schema/'.$connection->getName().'-schema.dump'))) {
            return $path;
        }

        return database_path('schema/'.$connection->getName().'-schema.sql');
    }
}
