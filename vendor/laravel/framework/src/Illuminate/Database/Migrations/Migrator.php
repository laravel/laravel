<?php namespace Illuminate\Database\Migrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class Migrator {

	/**
	 * The migration repository implementation.
	 *
	 * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
	 */
	protected $repository;

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The connection resolver instance.
	 *
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * The name of the default connection.
	 *
	 * @var string
	 */
	protected $connection;

	/**
	 * The notes for the current operation.
	 *
	 * @var array
	 */
	protected $notes = array();

	/**
	 * Create a new migrator instance.
	 *
	 * @param  \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
	 * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(MigrationRepositoryInterface $repository,
								Resolver $resolver,
                                Filesystem $files)
	{
		$this->files = $files;
		$this->resolver = $resolver;
		$this->repository = $repository;
	}

	/**
	 * Run the outstanding migrations at a given path.
	 *
	 * @param  string  $path
	 * @param  bool    $pretend
	 * @return void
	 */
	public function run($path, $pretend = false)
	{
		$this->notes = array();

		$this->requireFiles($path, $files = $this->getMigrationFiles($path));

		// Once we grab all of the migration files for the path, we will compare them
		// against the migrations that have already been run for this package then
		// run each of the outstanding migrations against a database connection.
		$ran = $this->repository->getRan();

		$migrations = array_diff($files, $ran);

		$this->runMigrationList($migrations, $pretend);
	}

	/**
	 * Run an array of migrations.
	 *
	 * @param  array  $migrations
	 * @param  bool   $pretend
	 * @return void
	 */
	public function runMigrationList($migrations, $pretend = false)
	{
		// First we will just make sure that there are any migrations to run. If there
		// aren't, we will just make a note of it to the developer so they're aware
		// that all of the migrations have been run against this database system.
		if (count($migrations) == 0)
		{
			$this->note('<info>Nothing to migrate.</info>');

			return;
		}

		$batch = $this->repository->getNextBatchNumber();

		// Once we have the array of migrations, we will spin through them and run the
		// migrations "up" so the changes are made to the databases. We'll then log
		// that the migration was run so we don't repeat it next time we execute.
		foreach ($migrations as $file)
		{
			$this->runUp($file, $batch, $pretend);
		}
	}

	/**
	 * Run "up" a migration instance.
	 *
	 * @param  string  $file
	 * @param  int     $batch
	 * @param  bool    $pretend
	 * @return void
	 */
	protected function runUp($file, $batch, $pretend)
	{
		// First we will resolve a "real" instance of the migration class from this
		// migration file name. Once we have the instances we can run the actual
		// command such as "up" or "down", or we can just simulate the action.
		$migration = $this->resolve($file);

		if ($pretend)
		{
			return $this->pretendToRun($migration, 'up');
		}

		$migration->up();

		// Once we have run a migrations class, we will log that it was run in this
		// repository so that we don't try to run it next time we do a migration
		// in the application. A migration repository keeps the migrate order.
		$this->repository->log($file, $batch);

		$this->note("<info>Migrated:</info> $file");
	}

	/**
	 * Rollback the last migration operation.
	 *
	 * @param  bool  $pretend
	 * @return int
	 */
	public function rollback($pretend = false)
	{
		$this->notes = array();

		// We want to pull in the last batch of migrations that ran on the previous
		// migration operation. We'll then reverse those migrations and run each
		// of them "down" to reverse the last migration "operation" which ran.
		$migrations = $this->repository->getLast();

		if (count($migrations) == 0)
		{
			$this->note('<info>Nothing to rollback.</info>');

			return count($migrations);
		}

		// We need to reverse these migrations so that they are "downed" in reverse
		// to what they run on "up". It lets us backtrack through the migrations
		// and properly reverse the entire database schema operation that ran.
		foreach ($migrations as $migration)
		{
			$this->runDown((object) $migration, $pretend);
		}

		return count($migrations);
	}

	/**
	 * Run "down" a migration instance.
	 *
	 * @param  object  $migration
	 * @param  bool    $pretend
	 * @return void
	 */
	protected function runDown($migration, $pretend)
	{
		$file = $migration->migration;

		// First we will get the file name of the migration so we can resolve out an
		// instance of the migration. Once we get an instance we can either run a
		// pretend execution of the migration or we can run the real migration.
		$instance = $this->resolve($file);

		if ($pretend)
		{
			return $this->pretendToRun($instance, 'down');
		}

		$instance->down();

		// Once we have successfully run the migration "down" we will remove it from
		// the migration repository so it will be considered to have not been run
		// by the application then will be able to fire by any later operation.
		$this->repository->delete($migration);

		$this->note("<info>Rolled back:</info> $file");
	}

	/**
	 * Get all of the migration files in a given path.
	 *
	 * @param  string  $path
	 * @return array
	 */
	public function getMigrationFiles($path)
	{
		$files = $this->files->glob($path.'/*_*.php');

		// Once we have the array of files in the directory we will just remove the
		// extension and take the basename of the file which is all we need when
		// finding the migrations that haven't been run against the databases.
		if ($files === false) return array();

		$files = array_map(function($file)
		{
			return str_replace('.php', '', basename($file));

		}, $files);

		// Once we have all of the formatted file names we will sort them and since
		// they all start with a timestamp this should give us the migrations in
		// the order they were actually created by the application developers.
		sort($files);

		return $files;
	}

	/**
	 * Require in all the migration files in a given path.
	 *
	 * @param  array  $files
	 * @return void
	 */
	public function requireFiles($path, array $files)
	{
		foreach ($files as $file) $this->files->requireOnce($path.'/'.$file.'.php');
	}

	/**
	 * Pretend to run the migrations.
	 *
	 * @param  object  $migration
	 * @return void
	 */
	protected function pretendToRun($migration, $method)
	{
		foreach ($this->getQueries($migration, $method) as $query)
		{
			$name = get_class($migration);

			$this->note("<info>{$name}:</info> {$query['query']}");
		}
	}

	/**
	 * Get all of the queries that would be run for a migration.
	 *
	 * @param  object  $migration
	 * @param  string  $method
	 * @return array
	 */
	protected function getQueries($migration, $method)
	{
		$connection = $migration->getConnection();

		// Now that we have the connections we can resolve it and pretend to run the
		// queries against the database returning the array of raw SQL statements
		// that would get fired against the database system for this migration.
		$db = $this->resolveConnection($connection);

		return $db->pretend(function() use ($migration, $method)
		{
			$migration->$method();
		});
	}

	/**
	 * Resolve a migration instance from a file.
	 *
	 * @param  string  $file
	 * @return object
	 */
	public function resolve($file)
	{
		$file = implode('_', array_slice(explode('_', $file), 4));

		$class = studly_case($file);

		return new $class;
	}

	/**
	 * Raise a note event for the migrator.
	 *
	 * @param  string  $message
	 * @return void
	 */
	protected function note($message)
	{
		$this->notes[] = $message;
	}

	/**
	 * Get the notes for the last operation.
	 *
	 * @return array
	 */
	public function getNotes()
	{
		return $this->notes;
	}

	/**
	 * Resolve the database connection instance.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function resolveConnection()
	{
		return $this->resolver->connection($this->connection);
	}

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setConnection($name)
	{
		if ( ! is_null($name))
		{
			$this->resolver->setDefaultConnection($name);
		}

		$this->repository->setSource($name);

		$this->connection = $name;
	}

	/**
	 * Get the migration repository instance.
	 *
	 * @return \Illuminate\Database\Migrations\MigrationRepositoryInterface
	 */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * Determine if the migration repository exists.
	 *
	 * @return bool
	 */
	public function repositoryExists()
	{
		return $this->repository->repositoryExists();
	}

	/**
	 * Get the file system instance.
	 *
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}

}
