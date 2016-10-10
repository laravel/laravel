<?php namespace Illuminate\Database\Migrations;

use Illuminate\Database\ConnectionResolverInterface as Resolver;

class DatabaseMigrationRepository implements MigrationRepositoryInterface {

	/**
	 * The database connection resolver instance.
	 *
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * The name of the migration table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The name of the database connection to use.
	 *
	 * @var string
	 */
	protected $connection;

	/**
	 * Create a new database migration repository instance.
	 *
	 * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(Resolver $resolver, $table)
	{
		$this->table = $table;
		$this->resolver = $resolver;
	}

	/**
	 * Get the ran migrations.
	 *
	 * @return array
	 */
	public function getRan()
	{
		return $this->table()->lists('migration');
	}

	/**
	 * Get the last migration batch.
	 *
	 * @return array
	 */
	public function getLast()
	{
		$query = $this->table()->where('batch', $this->getLastBatchNumber());

		return $query->orderBy('migration', 'desc')->get();
	}

	/**
	 * Log that a migration was run.
	 *
	 * @param  string  $file
	 * @param  int     $batch
	 * @return void
	 */
	public function log($file, $batch)
	{
		$record = array('migration' => $file, 'batch' => $batch);

		$this->table()->insert($record);
	}

	/**
	 * Remove a migration from the log.
	 *
	 * @param  object  $migration
	 * @return void
	 */
	public function delete($migration)
	{
		$this->table()->where('migration', $migration->migration)->delete();
	}

	/**
	 * Get the next migration batch number.
	 *
	 * @return int
	 */
	public function getNextBatchNumber()
	{
		return $this->getLastBatchNumber() + 1;
	}

	/**
	 * Get the last migration batch number.
	 *
	 * @return int
	 */
	public function getLastBatchNumber()
	{
		return $this->table()->max('batch');
	}

	/**
	 * Create the migration repository data store.
	 *
	 * @return void
	 */
	public function createRepository()
	{
		$schema = $this->getConnection()->getSchemaBuilder();

		$schema->create($this->table, function($table)
		{
			// The migrations table is responsible for keeping track of which of the
			// migrations have actually run for the application. We'll create the
			// table to hold the migration file's path as well as the batch ID.
			$table->string('migration');

			$table->integer('batch');
		});
	}

	/**
	 * Determine if the migration repository exists.
	 *
	 * @return bool
	 */
	public function repositoryExists()
	{
		$schema = $this->getConnection()->getSchemaBuilder();

		return $schema->hasTable($this->table);
	}

	/**
	 * Get a query builder for the migration table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function table()
	{
		return $this->getConnection()->table($this->table);
	}

	/**
	 * Get the connection resolver instance.
	 *
	 * @return \Illuminate\Database\ConnectionResolverInterface
	 */
	public function getConnectionResolver()
	{
		return $this->resolver;
	}

	/**
	 * Resolve the database connection instance.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->resolver->connection($this->connection);
	}

	/**
	 * Set the information source to gather data.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setSource($name)
	{
		$this->connection = $name;
	}

}
