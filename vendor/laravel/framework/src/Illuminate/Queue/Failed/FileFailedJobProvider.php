<?php

namespace Illuminate\Queue\Failed;

use Closure;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class FileFailedJobProvider implements CountableFailedJobProvider, FailedJobProviderInterface, PrunableFailedJobProvider
{
    /**
     * The file path where the failed job file should be stored.
     *
     * @var string
     */
    protected $path;

    /**
     * The maximum number of failed jobs to retain.
     *
     * @var int
     */
    protected $limit;

    /**
     * The lock provider resolver.
     *
     * @var \Closure
     */
    protected $lockProviderResolver;

    /**
     * Create a new file failed job provider.
     *
     * @param  string  $path
     * @param  int  $limit
     * @param  \Closure|null  $lockProviderResolver
     */
    public function __construct($path, $limit = 100, ?Closure $lockProviderResolver = null)
    {
        $this->path = $path;
        $this->limit = $limit;
        $this->lockProviderResolver = $lockProviderResolver;
    }

    /**
     * Log a failed job into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @param  \Throwable  $exception
     * @return int|null
     */
    public function log($connection, $queue, $payload, $exception)
    {
        return $this->lock(function () use ($connection, $queue, $payload, $exception) {
            $id = json_decode($payload, true)['uuid'];

            $jobs = $this->read();

            $failedAt = Date::now();

            array_unshift($jobs, [
                'id' => $id,
                'connection' => $connection,
                'queue' => $queue,
                'payload' => $payload,
                'exception' => (string) mb_convert_encoding($exception, 'UTF-8'),
                'failed_at' => $failedAt->format('Y-m-d H:i:s'),
                'failed_at_timestamp' => $failedAt->getTimestamp(),
            ]);

            $this->write(array_slice($jobs, 0, $this->limit));

            return $id;
        });
    }

    /**
     * Get the IDs of all of the failed jobs.
     *
     * @param  string|null  $queue
     * @return array
     */
    public function ids($queue = null)
    {
        return (new Collection($this->all()))
            ->when(! is_null($queue), fn ($collect) => $collect->where('queue', $queue))
            ->pluck('id')
            ->all();
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all()
    {
        return $this->read();
    }

    /**
     * Get a single failed job.
     *
     * @param  mixed  $id
     * @return object|null
     */
    public function find($id)
    {
        return (new Collection($this->read()))
            ->first(fn ($job) => $job->id === $id);
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function forget($id)
    {
        return $this->lock(function () use ($id) {
            $this->write($pruned = (new Collection($jobs = $this->read()))
                ->reject(fn ($job) => $job->id === $id)
                ->values()
                ->all());

            return count($jobs) !== count($pruned);
        });
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @param  int|null  $hours
     * @return void
     */
    public function flush($hours = null)
    {
        $this->prune(Date::now()->subHours($hours ?: 0));
    }

    /**
     * Prune all of the entries older than the given date.
     *
     * @param  \DateTimeInterface  $before
     * @return int
     */
    public function prune(DateTimeInterface $before)
    {
        return $this->lock(function () use ($before) {
            $jobs = $this->read();

            $this->write($prunedJobs = (new Collection($jobs))
                ->reject(fn ($job) => $job->failed_at_timestamp <= $before->getTimestamp())
                ->values()
                ->all()
            );

            return count($jobs) - count($prunedJobs);
        });
    }

    /**
     * Execute the given callback while holding a lock.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    protected function lock(Closure $callback)
    {
        if (! $this->lockProviderResolver) {
            return $callback();
        }

        return ($this->lockProviderResolver)()
            ->lock('laravel-failed-jobs', 5)
            ->block(10, function () use ($callback) {
                return $callback();
            });
    }

    /**
     * Read the failed jobs file.
     *
     * @return array
     */
    protected function read()
    {
        if (! file_exists($this->path)) {
            return [];
        }

        $content = file_get_contents($this->path);

        if (empty(trim($content))) {
            return [];
        }

        $content = json_decode($content);

        return is_array($content) ? $content : [];
    }

    /**
     * Write the given array of jobs to the failed jobs file.
     *
     * @param  array  $jobs
     * @return void
     */
    protected function write(array $jobs)
    {
        file_put_contents(
            $this->path,
            json_encode($jobs, JSON_PRETTY_PRINT)
        );
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
        if (($connection ?? $queue) === null) {
            return count($this->read());
        }

        return (new Collection($this->read()))
            ->filter(fn ($job) => $job->connection === ($connection ?? $job->connection) && $job->queue === ($queue ?? $job->queue))
            ->count();
    }
}
