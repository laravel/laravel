<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Jobs\BeanstalkdJob;
use Pheanstalk\Contract\JobIdInterface;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\Job;
use Pheanstalk\Values\JobId;
use Pheanstalk\Values\TubeName;

class BeanstalkdQueue extends Queue implements QueueContract
{
    /**
     * The Pheanstalk instance.
     *
     * @var \Pheanstalk\Contract\PheanstalkManagerInterface&\Pheanstalk\Contract\PheanstalkPublisherInterface&\Pheanstalk\Contract\PheanstalkSubscriberInterface
     */
    protected $pheanstalk;

    /**
     * The name of the default tube.
     *
     * @var string
     */
    protected $default;

    /**
     * The "time to run" for all pushed jobs.
     *
     * @var int
     */
    protected $timeToRun;

    /**
     * The maximum number of seconds to block for a job.
     *
     * @var int
     */
    protected $blockFor;

    /**
     * Create a new Beanstalkd queue instance.
     *
     * @param  \Pheanstalk\Contract\PheanstalkManagerInterface&\Pheanstalk\Contract\PheanstalkPublisherInterface&\Pheanstalk\Contract\PheanstalkSubscriberInterface  $pheanstalk
     * @param  string  $default
     * @param  int  $timeToRun
     * @param  int  $blockFor
     * @param  bool  $dispatchAfterCommit
     */
    public function __construct(
        $pheanstalk,
        $default,
        $timeToRun,
        $blockFor = 0,
        $dispatchAfterCommit = false,
    ) {
        $this->default = $default;
        $this->blockFor = $blockFor;
        $this->timeToRun = $timeToRun;
        $this->pheanstalk = $pheanstalk;
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
        $stats = $this->pheanstalk->statsTube(new TubeName($this->getQueue($queue)));

        return $stats->currentJobsReady
            + $stats->currentJobsDelayed
            + $stats->currentJobsReserved;
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return $this->pheanstalk->statsTube(new TubeName($this->getQueue($queue)))->currentJobsReady;
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return $this->pheanstalk->statsTube(new TubeName($this->getQueue($queue)))->currentJobsDelayed;
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return $this->pheanstalk->statsTube(new TubeName($this->getQueue($queue)))->currentJobsReserved;
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        // Not supported by Beanstalkd...
        return null;
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
        $this->pheanstalk->useTube(new TubeName($this->getQueue($queue)));

        return $this->pheanstalk->put(
            $payload, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, $this->timeToRun
        );
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
                $this->pheanstalk->useTube(new TubeName($this->getQueue($queue)));

                return $this->pheanstalk->put(
                    $payload,
                    Pheanstalk::DEFAULT_PRIORITY,
                    $this->secondsUntil($delay),
                    $this->timeToRun
                );
            }
        );
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
        foreach ((array) $jobs as $job) {
            if (isset($job->delay)) {
                $this->later($job->delay, $job, $data, $queue);
            } else {
                $this->push($job, $data, $queue);
            }
        }
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $this->pheanstalk->watch(
            $tube = new TubeName($queue = $this->getQueue($queue))
        );

        foreach ($this->pheanstalk->listTubesWatched() as $watched) {
            if ($watched->value !== $tube->value) {
                $this->pheanstalk->ignore($watched);
            }
        }

        $job = $this->pheanstalk->reserveWithTimeout($this->blockFor);

        if ($job instanceof JobIdInterface) {
            return new BeanstalkdJob(
                $this->container, $this->pheanstalk, $job, $this->connectionName, $queue
            );
        }
    }

    /**
     * Delete a message from the Beanstalk queue.
     *
     * @param  string  $queue
     * @param  string|int  $id
     * @return void
     */
    public function deleteMessage($queue, $id)
    {
        $this->pheanstalk->useTube(new TubeName($this->getQueue($queue)));

        $this->pheanstalk->delete(new Job(new JobId($id), ''));
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
     * Get the underlying Pheanstalk instance.
     *
     * @return \Pheanstalk\Contract\PheanstalkManagerInterface&\Pheanstalk\Contract\PheanstalkPublisherInterface&\Pheanstalk\Contract\PheanstalkSubscriberInterface
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }
}
