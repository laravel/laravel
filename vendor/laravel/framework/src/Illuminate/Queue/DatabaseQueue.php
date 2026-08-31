<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Database\Connection;
use Illuminate\Queue\Jobs\DatabaseJob;
use Illuminate\Queue\Jobs\DatabaseJobRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use PDO;
use Throwable;

class DatabaseQueue extends Queue implements QueueContract, ClearableQueue
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The database table that holds the jobs.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $retryAfter = 60;

    /**
     * Create a new database queue instance.
     *
     * @param  \Illuminate\Database\Connection  $database
     * @param  string  $table
     * @param  string  $default
     * @param  int  $retryAfter
     * @param  bool  $dispatchAfterCommit
     */
    public function __construct(
        Connection $database,
        $table,
        $default = 'default',
        $retryAfter = 60,
        $dispatchAfterCommit = false,
    ) {
        $this->table = $table;
        $this->default = $default;
        $this->database = $database;
        $this->retryAfter = $retryAfter;
        $this->dispatchAfterCommit = $dispatchAfterCommit;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->count();
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->whereNull('reserved_at')
            ->where('available_at', '<=', $this->currentTime())
            ->count();
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->whereNull('reserved_at')
            ->where('available_at', '>', $this->currentTime())
            ->count();
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->whereNotNull('reserved_at')
            ->count();
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->whereNull('reserved_at')
            ->where('available_at', '<=', $this->currentTime())
            ->oldest('available_at')
            ->value('available_at');
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue,
            null,
            function ($payload, $queue) {
                return $this->pushToDatabase($queue, $payload);
            }
        );
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->pushToDatabase($queue, $payload);
    }

    /**
     * Push a new job onto the queue after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data, $delay),
            $queue,
            $delay,
            function ($payload, $queue, $delay) {
                return $this->pushToDatabase($queue, $payload, $delay);
            }
        );
    }

    /**
     * Push an array of jobs onto the queue.
     *
     * @param  array  $jobs
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function bulk($jobs, $data = '', $queue = null)
    {
        $queue = $this->getQueue($queue);

        $now = $this->availableAt();

        return $this->database->table($this->table)->insert((new Collection((array) $jobs))->map(
            function ($job) use ($queue, $data, $now) {
                return $this->buildDatabaseRecord(
                    $queue,
                    $this->createPayload($job, $this->getQueue($queue), $data),
                    isset($job->delay) ? $this->availableAt($job->delay) : $now,
                );
            }
        )->all());
    }

    /**
     * Release a reserved job back onto the queue after (n) seconds.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\DatabaseJobRecord  $job
     * @param  int  $delay
     * @return mixed
     */
    public function release($queue, $job, $delay)
    {
        return $this->pushToDatabase($queue, $job->payload, $delay, $job->attempts);
    }

    /**
     * Push a raw payload to the database with a given delay of (n) seconds.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  int  $attempts
     * @return mixed
     */
    protected function pushToDatabase($queue, $payload, $delay = 0, $attempts = 0)
    {
        return $this->database->table($this->table)->insertGetId($this->buildDatabaseRecord(
            $this->getQueue($queue),
            $payload,
            $this->availableAt($delay),
            $attempts
        ));
    }

    /**
     * Create an array to insert for the given job.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  int  $availableAt
     * @param  int  $attempts
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        return [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => $availableAt,
            'created_at' => $this->currentTime(),
            'payload' => $payload,
        ];
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     *
     * @throws \Throwable
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $jobRecord = null;

        try {
            return $this->database->transaction(function () use ($queue, &$jobRecord) {
                if ($jobRecord = $this->getNextAvailableJob($queue)) {
                    return $this->marshalJob($queue, $jobRecord);
                }
            });
        } catch (Throwable $e) {
            // Potentially invalid job that we need to fail (#58978)...
            if ($jobRecord) {
                try {
                    (new DatabaseJob(
                        $this->container, $this, $jobRecord, $this->connectionName, $queue
                    ))->fail($e);
                } catch (Throwable) {
                    // Ignore and throw the original exception...
                }
            }

            throw $e;
        }
    }

    /**
     * Get the next available job for the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Queue\Jobs\DatabaseJobRecord|null
     */
    protected function getNextAvailableJob($queue)
    {
        $job = $this->database->table($this->table)
            ->lock($this->getLockForPopping())
            ->where('queue', $this->getQueue($queue))
            ->where(function ($query) {
                $this->isAvailable($query);
                $this->isReservedButExpired($query);
            })
            ->orderBy('id', 'asc')
            ->first();

        return $job ? new DatabaseJobRecord((object) $job) : null;
    }

    /**
     * Get the lock required for popping the next job.
     *
     * @return string|bool
     */
    protected function getLockForPopping()
    {
        $databaseEngine = $this->database->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $databaseVersion = $this->database->getConfig('version') ?? $this->database->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if ((new Stringable($databaseVersion))->contains('MariaDB')) {
            $databaseEngine = 'mariadb';
            $databaseVersion = Str::before(Str::after($databaseVersion, '5.5.5-'), '-');
        } elseif ((new Stringable($databaseVersion))->contains(['vitess', 'PlanetScale'])) {
            $databaseEngine = 'vitess';
            $databaseVersion = Str::before($databaseVersion, '-');
        }

        if (($databaseEngine === 'mysql' && version_compare($databaseVersion, '8.0.1', '>=')) ||
            ($databaseEngine === 'mariadb' && version_compare($databaseVersion, '10.6.0', '>=')) ||
            ($databaseEngine === 'pgsql' && version_compare($databaseVersion, '9.5', '>=')) ||
            ($databaseEngine === 'vitess' && version_compare($databaseVersion, '19.0', '>='))
        ) {
            return 'FOR UPDATE SKIP LOCKED';
        }

        if ($databaseEngine === 'sqlsrv') {
            return 'with(rowlock,updlock,readpast)';
        }

        return true;
    }

    /**
     * Modify the query to check for available jobs.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return void
     */
    protected function isAvailable($query)
    {
        $query->where(function ($query) {
            $query->whereNull('reserved_at')
                ->where('available_at', '<=', $this->currentTime());
        });
    }

    /**
     * Modify the query to check for jobs that are reserved but have expired.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return void
     */
    protected function isReservedButExpired($query)
    {
        $expiration = Carbon::now()->subSeconds($this->retryAfter)->getTimestamp();

        $query->orWhere(function ($query) use ($expiration) {
            $query->where('reserved_at', '<=', $expiration);
        });
    }

    /**
     * Marshal the reserved job into a DatabaseJob instance.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\DatabaseJobRecord  $job
     * @return \Illuminate\Queue\Jobs\DatabaseJob
     */
    protected function marshalJob($queue, $job)
    {
        return new DatabaseJob(
            $this->container,
            $this,
            $this->markJobAsReserved($job),
            $this->connectionName,
            $queue,
        );
    }

    /**
     * Mark the given job ID as reserved.
     *
     * @param  \Illuminate\Queue\Jobs\DatabaseJobRecord  $job
     * @return \Illuminate\Queue\Jobs\DatabaseJobRecord
     */
    protected function markJobAsReserved($job)
    {
        $this->database->table($this->table)->where('id', $job->id)->update([
            'reserved_at' => $job->touch(),
            'attempts' => $job->increment(),
        ]);

        return $job;
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param  string  $queue
     * @param  string  $id
     * @return void
     *
     * @throws \Throwable
     */
    public function deleteReserved($queue, $id)
    {
        $this->database->transaction(function () use ($id) {
            if ($this->database->table($this->table)->lockForUpdate()->find($id)) {
                $this->database->table($this->table)->where('id', $id)->delete();
            }
        });
    }

    /**
     * Delete a reserved job from the reserved queue and release it.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\DatabaseJob  $job
     * @param  int  $delay
     * @return void
     */
    public function deleteAndRelease($queue, $job, $delay)
    {
        $this->database->transaction(function () use ($queue, $job, $delay) {
            if ($this->database->table($this->table)->lockForUpdate()->find($job->getJobId())) {
                $this->database->table($this->table)->where('id', $job->getJobId())->delete();
            }

            $this->release($queue, $job->getJobRecord(), $delay);
        });
    }

    /**
     * Delete all of the jobs from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue)
    {
        return $this->database->table($this->table)
            ->where('queue', $this->getQueue($queue))
            ->delete();
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying database instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
