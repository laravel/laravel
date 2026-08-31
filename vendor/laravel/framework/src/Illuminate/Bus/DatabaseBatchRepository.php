<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Throwable;

class DatabaseBatchRepository implements PrunableBatchRepository
{
    /**
     * The batch factory instance.
     *
     * @var \Illuminate\Bus\BatchFactory
     */
    protected $factory;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The database table to use to store batch information.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new batch repository instance.
     *
     * @param  \Illuminate\Bus\BatchFactory  $factory
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     */
    public function __construct(BatchFactory $factory, Connection $connection, string $table)
    {
        $this->factory = $factory;
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Retrieve a list of batches.
     *
     * @param  int  $limit
     * @param  mixed  $before
     * @return \Illuminate\Bus\Batch[]
     */
    public function get($limit = 50, $before = null)
    {
        return $this->connection->table($this->table)
            ->orderByDesc('id')
            ->limit($limit)
            ->when($before, fn ($q) => $q->where('id', '<', $before))
            ->get()
            ->map(function ($batch) {
                return $this->toBatch($batch);
            })
            ->all();
    }

    /**
     * Retrieve information about an existing batch.
     *
     * @param  string  $batchId
     * @return \Illuminate\Bus\Batch|null
     */
    public function find(string $batchId)
    {
        $batch = $this->connection->table($this->table)
            ->useWritePdo()
            ->where('id', $batchId)
            ->first();

        if ($batch) {
            return $this->toBatch($batch);
        }
    }

    /**
     * Store a new pending batch.
     *
     * @param  \Illuminate\Bus\PendingBatch  $batch
     * @return \Illuminate\Bus\Batch
     */
    public function store(PendingBatch $batch)
    {
        $id = (string) Str::orderedUuid();

        $this->connection->table($this->table)->insert([
            'id' => $id,
            'name' => $batch->name,
            'total_jobs' => 0,
            'pending_jobs' => 0,
            'failed_jobs' => 0,
            'failed_job_ids' => '[]',
            'options' => $this->serialize($batch->options),
            'created_at' => time(),
            'cancelled_at' => null,
            'finished_at' => null,
        ]);

        return $this->find($id);
    }

    /**
     * Increment the total number of jobs within the batch.
     *
     * @param  string  $batchId
     * @param  int  $amount
     * @return void
     */
    public function incrementTotalJobs(string $batchId, int $amount)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'total_jobs' => new Expression('total_jobs + '.$amount),
            'pending_jobs' => new Expression('pending_jobs + '.$amount),
            'finished_at' => null,
        ]);
    }

    /**
     * Decrement the total number of pending jobs for the batch.
     *
     * @param  string  $batchId
     * @param  string  $jobId
     * @return \Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function decrementPendingJobs(string $batchId, string $jobId)
    {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
            return [
                'pending_jobs' => $batch->pending_jobs - 1,
                'failed_jobs' => $batch->failed_jobs,
                'failed_job_ids' => json_encode(array_values(array_diff((array) json_decode($batch->failed_job_ids, true), [$jobId]))),
            ];
        });

        return new UpdatedBatchJobCounts(
            $values['pending_jobs'],
            $values['failed_jobs']
        );
    }

    /**
     * Increment the total number of failed jobs for the batch.
     *
     * @param  string  $batchId
     * @param  string  $jobId
     * @return \Illuminate\Bus\UpdatedBatchJobCounts
     */
    public function incrementFailedJobs(string $batchId, string $jobId)
    {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
            return [
                'pending_jobs' => $batch->pending_jobs,
                'failed_jobs' => $batch->failed_jobs + 1,
                'failed_job_ids' => json_encode(array_values(array_unique(array_merge((array) json_decode($batch->failed_job_ids, true), [$jobId])))),
            ];
        });

        return new UpdatedBatchJobCounts(
            $values['pending_jobs'],
            $values['failed_jobs']
        );
    }

    /**
     * Update an atomic value within the batch.
     *
     * @param  string  $batchId
     * @param  \Closure  $callback
     * @return int|null
     */
    protected function updateAtomicValues(string $batchId, Closure $callback)
    {
        return $this->connection->transaction(function () use ($batchId, $callback) {
            $batch = $this->connection->table($this->table)->where('id', $batchId)
                ->lockForUpdate()
                ->first();

            return is_null($batch) ? [] : tap($callback($batch), function ($values) use ($batchId) {
                $this->connection->table($this->table)->where('id', $batchId)->update($values);
            });
        });
    }

    /**
     * Mark the batch that has the given ID as finished.
     *
     * @param  string  $batchId
     * @return void
     */
    public function markAsFinished(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'finished_at' => time(),
        ]);
    }

    /**
     * Cancel the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function cancel(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'cancelled_at' => time(),
            'finished_at' => time(),
        ]);
    }

    /**
     * Delete the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function delete(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->delete();
    }

    /**
     * Prune all of the entries older than the given date.
     *
     * @param  \DateTimeInterface  $before
     * @return int
     */
    public function prune(DateTimeInterface $before)
    {
        $query = $this->connection->table($this->table)
            ->whereNotNull('finished_at')
            ->where('finished_at', '<', $before->getTimestamp());

        $totalDeleted = 0;

        do {
            $deleted = $query->limit(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Prune all of the unfinished entries older than the given date.
     *
     * @param  \DateTimeInterface  $before
     * @return int
     */
    public function pruneUnfinished(DateTimeInterface $before)
    {
        $query = $this->connection->table($this->table)
            ->whereNull('finished_at')
            ->where('created_at', '<', $before->getTimestamp());

        $totalDeleted = 0;

        do {
            $deleted = $query->limit(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Prune all of the cancelled entries older than the given date.
     *
     * @param  \DateTimeInterface  $before
     * @return int
     */
    public function pruneCancelled(DateTimeInterface $before)
    {
        $query = $this->connection->table($this->table)
            ->whereNotNull('cancelled_at')
            ->where('created_at', '<', $before->getTimestamp());

        $totalDeleted = 0;

        do {
            $deleted = $query->limit(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }

    /**
     * Execute the given Closure within a storage specific transaction.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        return $this->connection->transaction(fn () => $callback());
    }

    /**
     * Rollback the last database transaction for the connection.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->connection->rollBack(toLevel: 0);
    }

    /**
     * Serialize the given value.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serialize($value)
    {
        $serialized = serialize($value);

        return $this->connection instanceof PostgresConnection
            ? base64_encode($serialized)
            : $serialized;
    }

    /**
     * Unserialize the given value.
     *
     * @param  string  $serialized
     * @return mixed
     */
    protected function unserialize($serialized)
    {
        if ($this->connection instanceof PostgresConnection &&
            ! Str::contains($serialized, [':', ';'])) {
            $serialized = base64_decode($serialized);
        }

        try {
            return unserialize($serialized);
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * Convert the given raw batch to a Batch object.
     *
     * @param  object  $batch
     * @return \Illuminate\Bus\Batch
     */
    protected function toBatch($batch)
    {
        return $this->factory->make(
            $this,
            $batch->id,
            $batch->name,
            (int) $batch->total_jobs,
            (int) $batch->pending_jobs,
            (int) $batch->failed_jobs,
            (array) json_decode($batch->failed_job_ids, true),
            $this->unserialize($batch->options),
            CarbonImmutable::createFromTimestamp($batch->created_at, date_default_timezone_get()),
            $batch->cancelled_at ? CarbonImmutable::createFromTimestamp($batch->cancelled_at, date_default_timezone_get()) : $batch->cancelled_at,
            $batch->finished_at ? CarbonImmutable::createFromTimestamp($batch->finished_at, date_default_timezone_get()) : $batch->finished_at
        );
    }

    /**
     * Get the underlying database connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the underlying database connection.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return void
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }
}
