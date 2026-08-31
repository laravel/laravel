<?php

namespace Illuminate\Support\Testing\Fakes;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Support\Str;

class BatchRepositoryFake implements BatchRepository
{
    /**
     * The batches stored in the repository.
     *
     * @var \Illuminate\Bus\Batch[]
     */
    protected $batches = [];

    /**
     * Retrieve a list of batches.
     *
     * @param  int  $limit
     * @param  mixed  $before
     * @return \Illuminate\Bus\Batch[]
     */
    public function get($limit, $before)
    {
        return $this->batches;
    }

    /**
     * Retrieve information about an existing batch.
     *
     * @param  string  $batchId
     * @return \Illuminate\Bus\Batch|null
     */
    public function find(string $batchId)
    {
        return $this->batches[$batchId] ?? null;
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

        $this->batches[$id] = new BatchFake(
            $id,
            $batch->name,
            count($batch->jobs),
            count($batch->jobs),
            0,
            [],
            $batch->options,
            CarbonImmutable::now(),
            null,
            null
        );

        return $this->batches[$id];
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
        //
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
        return new UpdatedBatchJobCounts;
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
        return new UpdatedBatchJobCounts;
    }

    /**
     * Mark the batch that has the given ID as finished.
     *
     * @param  string  $batchId
     * @return void
     */
    public function markAsFinished(string $batchId)
    {
        if (isset($this->batches[$batchId])) {
            $this->batches[$batchId]->finishedAt = now();
        }
    }

    /**
     * Cancel the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function cancel(string $batchId)
    {
        if (isset($this->batches[$batchId])) {
            $this->batches[$batchId]->cancel();
        }
    }

    /**
     * Delete the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function delete(string $batchId)
    {
        unset($this->batches[$batchId]);
    }

    /**
     * Execute the given Closure within a storage specific transaction.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        return $callback();
    }

    /**
     * Rollback the last database transaction for the connection.
     *
     * @return void
     */
    public function rollBack()
    {
        //
    }
}
