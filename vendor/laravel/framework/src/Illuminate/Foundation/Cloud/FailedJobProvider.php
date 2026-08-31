<?php

namespace Illuminate\Foundation\Cloud;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\Failed\CountableFailedJobProvider;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Queue\Failed\PrunableFailedJobProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FailedJobProvider implements FailedJobProviderInterface, CountableFailedJobProvider, PrunableFailedJobProvider
{
    /**
     * The connected queue instance.
     *
     * @var ?\Illuminate\Foundation\Cloud\Queue
     */
    protected $queue = null;

    /**
     * The loaded failed jobs keyed by ID.
     *
     * @var array<string, object>
     */
    protected $loadedFailedJobs = [];

    /**
     * Create a new instance.
     */
    public function __construct(
        protected FailedJobProviderInterface $failer,
        protected Events $events,
        protected StringEncrypter $encrypter,
    ) {
        //
    }

    /**
     * Log a failed job into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @param  \Throwable  $exception
     * @return string|null
     */
    public function log($connection, $queue, $payload, $exception)
    {
        if ($connection !== 'cloud') {
            return $this->failer->log(...func_get_args());
        }

        if ($this->queue === null) {
            throw new RuntimeException('The failed job provider does not have a configured queue');
        }

        $timestamp = CarbonImmutable::now('UTC');
        $processingJobDetails = $this->queue->processingJobDetails();

        $this->events->emit([
            '_cloud_event' => 'failed_job',
            'id' => $id = Str::uuid7($timestamp)->toString(),
            'queue' => $processingJobDetails['queue'],
            'started_at' => $processingJobDetails['started_at']->toDateTimeString('microsecond'),
            'attempts' => $processingJobDetails['attempts'],
            'payload' => $payload,
            'exception' => (string) mb_convert_encoding($exception, 'UTF-8'),
        ]);

        $this->queue->finishProcessingJob(timestamp: $timestamp);

        return $id;
    }

    /**
     * Get the IDs of all of the failed jobs.
     *
     * @param  string|null  $queue
     * @return array
     */
    public function ids($queue = null)
    {
        return $this->failer->ids(...func_get_args());
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all()
    {
        return $this->failer->all(...func_get_args());
    }

    /**
     * Get a single failed job.
     *
     * @param  mixed  $id
     * @return object|null
     */
    public function find($id)
    {
        if (! str_starts_with($id, 'https://')) {
            return $this->failer->find($id);
        }

        $response = Http::connectTimeout(10)
            ->timeout(10)
            ->retry(3, 1000, fn ($exception) => $exception instanceof ConnectionException)
            ->throw()
            ->get($id);

        return $this->loadedFailedJobs[$id] = json_decode($this->encrypter->decryptString($response->body()), flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function forget($id)
    {
        if (! str_starts_with($id, 'https://')) {
            return $this->failer->forget($id);
        }

        if (is_null($job = $this->loadedFailedJobs[$id] ?? null)) {
            return false;
        }

        $this->events->emit([
            '_cloud_event' => 'failed_job',
            'id' => $job->id,
            'queue' => $job->queue,
            'retried_at' => CarbonImmutable::now('UTC')->toDateTimeString('microsecond'),
        ]);

        return true;
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @param  int|null  $hours
     * @return void
     */
    public function flush($hours = null)
    {
        $this->failer->flush(...func_get_args());
    }

    /**
     * Count the failed jobs.
     *
     * @param  string|null  $connection
     * @param  string|null  $queue
     * @return int
     */
    public function count($connection = null, $queue = null)
    {
        if (! $this->failer instanceof CountableFailedJobProvider) {
            return 0;
        }

        return $this->failer->count(...func_get_args());
    }

    /**
     * Prune all of the entries older than the given date.
     *
     * @param  \DateTimeInterface  $before
     * @return int
     */
    public function prune(DateTimeInterface $before)
    {
        if (! $this->failer instanceof PrunableFailedJobProvider) {
            return 0;
        }

        return $this->failer->prune(...func_get_args());
    }

    /**
     * Set the connected queue instance.
     *
     * @param  \Illuminate\Foundation\Cloud\Queue  $queue
     * @return $this
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
