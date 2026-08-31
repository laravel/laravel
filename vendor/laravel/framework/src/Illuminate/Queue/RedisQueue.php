<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\Jobs\RedisJob;
use Illuminate\Redis\Connections\PhpRedisClusterConnection;
use Illuminate\Redis\Connections\PredisClusterConnection;
use Illuminate\Support\Str;

class RedisQueue extends Queue implements QueueContract, ClearableQueue
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;

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
     * The maximum number of seconds to block for a job.
     *
     * @var int|null
     */
    protected $blockFor = null;

    /**
     * The batch size to use when migrating delayed / expired jobs onto the primary queue.
     *
     * Negative values are infinite.
     *
     * @var int
     */
    protected $migrationBatchSize = -1;

    /**
     * Indicates if a secondary queue had a job available between checks of the primary queue.
     *
     * Only applicable when monitoring multiple named queues with a single instance.
     *
     * @var bool
     */
    protected $secondaryQueueHadJob = false;

    /**
     * Create a new Redis queue instance.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string  $default
     * @param  string|null  $connection
     * @param  int  $retryAfter
     * @param  int|null  $blockFor
     * @param  bool  $dispatchAfterCommit
     * @param  int  $migrationBatchSize
     */
    public function __construct(
        Redis $redis,
        $default = 'default',
        $connection = null,
        $retryAfter = 60,
        $blockFor = null,
        $dispatchAfterCommit = false,
        $migrationBatchSize = -1,
    ) {
        $this->redis = $redis;
        $this->default = $default;
        $this->blockFor = $blockFor;
        $this->connection = $connection;
        $this->retryAfter = $retryAfter;
        $this->dispatchAfterCommit = $dispatchAfterCommit;
        $this->migrationBatchSize = $migrationBatchSize;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $queue = $this->getQueue($queue);

        return $this->getConnection()->eval(
            LuaScripts::size(), 3, $queue, $queue.':delayed', $queue.':reserved'
        );
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return $this->getConnection()->llen($this->getQueue($queue));
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return $this->getConnection()->zcard($this->getQueue($queue).':delayed');
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return $this->getConnection()->zcard($this->getQueue($queue).':reserved');
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        $payload = $this->getConnection()->lindex($this->getQueue($queue), 0);

        if (! $payload) {
            return null;
        }

        $data = json_decode($payload, true);

        return $data['createdAt'] ?? null;
    }

    /**
     * Push an array of jobs onto the queue.
     *
     * @param  array  $jobs
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return void
     */
    public function bulk($jobs, $data = '', $queue = null)
    {
        $connection = $this->getConnection();

        $bulk = function () use ($jobs, $data, $queue) {
            foreach ((array) $jobs as $job) {
                if (isset($job->delay)) {
                    $this->later($job->delay, $job, $data, $queue);
                } else {
                    $this->push($job, $data, $queue);
                }
            }
        };

        if ($connection instanceof PhpRedisClusterConnection) {
            $connection->transaction($bulk);
        } elseif ($connection instanceof PredisClusterConnection) {
            $connection->pipeline($bulk);
        } else {
            $connection->pipeline(fn () => $connection->transaction($bulk));
        }
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  object|string  $job
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
                return $this->pushRaw($payload, $queue);
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
        $this->getConnection()->eval(
            LuaScripts::push(), 2, $this->getQueue($queue),
            $this->getQueue($queue).':notify', $payload
        );

        return json_decode($payload, true)['id'] ?? null;
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  object|string  $job
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
                return $this->laterRaw($delay, $payload, $queue);
            }
        );
    }

    /**
     * Push a raw job onto the queue after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $payload
     * @param  string|null  $queue
     * @return mixed
     */
    protected function laterRaw($delay, $payload, $queue = null)
    {
        $this->getConnection()->eval(
            LuaScripts::later(), 1, $this->getQueue($queue).':delayed',
            $this->availableAt($delay), $payload
        );

        return json_decode($payload, true)['id'] ?? null;
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string  $job
     * @param  string  $queue
     * @param  mixed  $data
     * @return array
     */
    protected function createPayloadArray($job, $queue, $data = '')
    {
        return array_merge(parent::createPayloadArray($job, $queue, $data), [
            'id' => $this->getRandomId(),
            'attempts' => 0,
        ]);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @param  int  $index
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null, $index = 0)
    {
        $this->migrate($prefixed = $this->getQueue($queue));

        $block = ! $this->secondaryQueueHadJob && $index == 0;

        [$job, $reserved] = $this->retrieveNextJob($prefixed, $block);

        if ($index == 0) {
            $this->secondaryQueueHadJob = false;
        }

        if ($reserved) {
            if ($index > 0) {
                $this->secondaryQueueHadJob = true;
            }

            return new RedisJob(
                $this->container, $this, $job,
                $reserved, $this->connectionName, $queue ?: $this->default
            );
        }
    }

    /**
     * Migrate any delayed or expired jobs onto the primary queue.
     *
     * @param  string  $queue
     * @return void
     */
    protected function migrate($queue)
    {
        $this->migrateExpiredJobs($queue.':delayed', $queue);

        if (! is_null($this->retryAfter)) {
            $this->migrateExpiredJobs($queue.':reserved', $queue);
        }
    }

    /**
     * Migrate the delayed jobs that are ready to the regular queue.
     *
     * @param  string  $from
     * @param  string  $to
     * @return array
     */
    public function migrateExpiredJobs($from, $to)
    {
        return $this->getConnection()->eval(
            LuaScripts::migrateExpiredJobs(), 3, $from, $to, $to.':notify', $this->currentTime(), $this->migrationBatchSize
        );
    }

    /**
     * Retrieve the next job from the queue.
     *
     * @param  string  $queue
     * @param  bool  $block
     * @return array
     */
    protected function retrieveNextJob($queue, $block = true)
    {
        $nextJob = $this->getConnection()->eval(
            LuaScripts::pop(), 3, $queue, $queue.':reserved', $queue.':notify',
            $this->availableAt($this->retryAfter)
        );

        if (empty($nextJob)) {
            return [null, null];
        }

        [$job, $reserved] = $nextJob;

        if (! $job && ! is_null($this->blockFor) && $block &&
            $this->getConnection()->blpop([$queue.':notify'], $this->blockFor)) {
            return $this->retrieveNextJob($queue, false);
        }

        return [$job, $reserved];
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\RedisJob  $job
     * @return void
     */
    public function deleteReserved($queue, $job)
    {
        $this->getConnection()->zrem($this->getQueue($queue).':reserved', $job->getReservedJob());
    }

    /**
     * Delete a reserved job from the reserved queue and release it.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\Jobs\RedisJob  $job
     * @param  int  $delay
     * @return void
     */
    public function deleteAndRelease($queue, $job, $delay)
    {
        $queue = $this->getQueue($queue);

        $this->getConnection()->eval(
            LuaScripts::release(), 2, $queue.':delayed', $queue.':reserved',
            $job->getReservedJob(), $this->availableAt($delay)
        );
    }

    /**
     * Delete all of the jobs from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue)
    {
        $queue = $this->getQueue($queue);

        return $this->getConnection()->eval(
            LuaScripts::clear(), 4, $queue, $queue.':delayed',
            $queue.':reserved', $queue.':notify'
        );
    }

    /**
     * Get a random ID string.
     *
     * @return string
     */
    protected function getRandomId()
    {
        return Str::random(32);
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return 'queues:'.($queue ?: $this->default);
    }

    /**
     * Get the connection for the queue.
     *
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function getConnection()
    {
        return $this->redis->connection($this->connection);
    }

    /**
     * Get the underlying Redis instance.
     *
     * @return \Illuminate\Contracts\Redis\Factory
     */
    public function getRedis()
    {
        return $this->redis;
    }
}
