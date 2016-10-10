<?php namespace Illuminate\Queue\Failed;

use Carbon\Carbon;
use Illuminate\Database\ConnectionResolverInterface;

class DatabaseFailedJobProvider implements FailedJobProviderInterface {

	/**
	 * The connection resolver implementation.
	 *
	 * @var \Illuminate\Database\ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * The database connection name.
	 *
	 * @var string
	 */
	protected $database;

	/**
	 * The database table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Create a new database failed job provider.
	 *
	 * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
	 * @param  string  $database
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(ConnectionResolverInterface $resolver, $database, $table)
	{
		$this->table = $table;
		$this->resolver = $resolver;
		$this->database = $database;
	}

	/**
	 * Log a failed job into storage.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  string  $payload
	 * @return void
	 */
	public function log($connection, $queue, $payload)
	{
		$failed_at = Carbon::now();

		$this->getTable()->insert(compact('connection', 'queue', 'payload', 'failed_at'));
	}

	/**
	 * Get a list of all of the failed jobs.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->getTable()->orderBy('id', 'desc')->get();
	}

	/**
	 * Get a single failed job.
	 *
	 * @param  mixed  $id
	 * @return array
	 */
	public function find($id)
	{
		return $this->getTable()->find($id);
	}

	/**
	 * Delete a single failed job from storage.
	 *
	 * @param  mixed  $id
	 * @return bool
	 */
	public function forget($id)
	{
		return $this->getTable()->where('id', $id)->delete() > 0;
	}

	/**
	 * Flush all of the failed jobs from storage.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->getTable()->delete();
	}

	/**
	 * Get a new query builder instance for the table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function getTable()
	{
		return $this->resolver->connection($this->database)->table($this->table);
	}

}
