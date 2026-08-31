<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueManager;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobPopped;
use Illuminate\Queue\Events\JobPopping;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Queue\Events\JobTimedOut;
use Illuminate\Queue\Events\Looping;
use Illuminate\Queue\Events\WorkerStarting;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Carbon;
use Throwable;

class Worker
{
    use DetectsLostConnections;

    const EXIT_SUCCESS = 0;
    const EXIT_ERROR = 1;
    const EXIT_MEMORY_LIMIT = 12;

    /**
     * The name of the worker.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $manager;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * The callback used to determine if the application is in maintenance mode.
     *
     * @var callable
     */
    protected $isDownForMaintenance;

    /**
     * The callback used to reset the application's scope.
     *
     * @var callable
     */
    protected $resetScope;

    /**
     * Indicates if the worker should exit.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * Indicates if the worker lost its connection.
     *
     * @var bool
     */
    public $lostConnection = false;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    public $paused = false;

    /**
     * The callbacks used to pop jobs from queues.
     *
     * @var callable[]
     */
    protected static $popCallbacks = [];

    /**
     * The custom exit code to be used when memory is exceeded.
     *
     * @var int|null
     */
    public static $memoryExceededExitCode;

    /**
     * Indicates if the worker should check for the restart signal in the cache.
     *
     * @var bool
     */
    public static $restartable = true;

    /**
     * Indicates if the worker should check for the paused signal in the cache.
     *
     * @var bool
     */
    public static $pausable = true;

    /**
     * Create a new queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $manager
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptions
     * @param  callable  $isDownForMaintenance
     * @param  callable|null  $resetScope
     */
    public function __construct(
        QueueManager $manager,
        Dispatcher $events,
        ExceptionHandler $exceptions,
        callable $isDownForMaintenance,
        ?callable $resetScope = null,
    ) {
        $this->events = $events;
        $this->manager = $manager;
        $this->exceptions = $exceptions;
        $this->isDownForMaintenance = $isDownForMaintenance;
        $this->resetScope = $resetScope;
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        if ($supportsAsyncSignals = $this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $lastRestart = $this->getTimestampOfLastQueueRestart();

        [$startTime, $jobsProcessed] = [$this->currentTime(), 0];

        $lastJobProcessedAt = $startTime;

        $this->raiseWorkerStartingEvent($connectionName, $queue, $options);

        while (true) {
            // Before reserving any jobs, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
            if (! $this->daemonShouldRun($options, $connectionName, $queue)) {
                [$status, $reason] = $this->pauseWorker(
                    $options, $lastRestart, $startTime, $jobsProcessed, $lastJobProcessedAt
                );

                if (! is_null($status)) {
                    return $this->stop($status, $options, $reason);
                }

                continue;
            }

            if (isset($this->resetScope)) {
                ($this->resetScope)();
            }

            // First, we will attempt to get the next job off of the queue. We will also
            // register the timeout handler and reset the alarm for this job so it is
            // not stuck in a frozen state forever. Then, we can fire off this job.
            $job = $this->getNextJob(
                $this->manager->connection($connectionName), $queue
            );

            if ($supportsAsyncSignals) {
                $this->registerTimeoutHandler($job, $options);
            }

            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this job for processing. Otherwise, we will need to sleep the
            // worker so no more jobs are processed until they should be processed.
            if ($job) {
                $jobsProcessed++;

                $this->runJob($job, $connectionName, $options);

                $lastJobProcessedAt = $this->currentTime();

                if ($options->rest > 0) {
                    $this->sleep($options->rest);
                }
            } else {
                $this->sleep($options->sleep);
            }

            if ($supportsAsyncSignals) {
                $this->resetTimeoutHandler();
            }

            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
            [$status, $reason] = $this->stopIfNecessary(
                $options, $lastRestart, $startTime, $jobsProcessed, $job, $lastJobProcessedAt
            );

            if (! is_null($status)) {
                return $this->stop($status, $options, $reason);
            }
        }
    }

    /**
     * Register the worker timeout handler.
     *
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function registerTimeoutHandler($job, WorkerOptions $options)
    {
        // We will register a signal handler for the alarm signal so that we can kill this
        // process if it is running too long because it has frozen. This uses the async
        // signals supported in recent versions of PHP to accomplish it conveniently.
        pcntl_signal(SIGALRM, function () use ($job, $options) {
            if ($job) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $job->getConnectionName(), $job, (int) $options->maxTries, $e = $this->timeoutExceededException($job)
                );

                $this->markJobAsFailedIfWillExceedMaxExceptions(
                    $job->getConnectionName(), $job, $e
                );

                $this->markJobAsFailedIfItShouldFailOnTimeout(
                    $job->getConnectionName(), $job, $e
                );

                $this->events->dispatch(new JobTimedOut(
                    $job->getConnectionName(), $job
                ));
            }

            $this->kill(static::EXIT_ERROR, $options, WorkerStopReason::TimedOut);
        }, true);

        pcntl_alarm(
            max($this->timeoutForJob($job, $options), 0)
        );
    }

    /**
     * Reset the worker timeout handler.
     *
     * @return void
     */
    protected function resetTimeoutHandler()
    {
        pcntl_alarm(0);
    }

    /**
     * Get the appropriate timeout for the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    protected function timeoutForJob($job, WorkerOptions $options)
    {
        return $job && ! is_null($job->timeout()) ? $job->timeout() : $options->timeout;
    }

    /**
     * Determine if the daemon should process on this iteration.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  string  $connectionName
     * @param  string  $queue
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options, $connectionName, $queue)
    {
        return ! ((($this->isDownForMaintenance)() && ! $options->force) ||
            $this->paused ||
            $this->events->until(new Looping($connectionName, $queue)) === false);
    }

    /**
     * Pause the worker for the current loop.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @param  int|float  $startTime
     * @param  int  $jobsProcessed
     * @param  int|float|null  $lastJobProcessedAt
     * @return array|null
     */
    protected function pauseWorker(WorkerOptions $options, $lastRestart, $startTime = 0, $jobsProcessed = 0, $lastJobProcessedAt = null)
    {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);

        return $this->stopIfNecessary($options, $lastRestart, $startTime, $jobsProcessed, null, $lastJobProcessedAt);
    }

    /**
     * Determine the exit code to stop the process if necessary.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @param  int  $startTime
     * @param  int  $jobsProcessed
     * @param  mixed  $job
     * @param  int|float|null  $lastJobProcessedAt
     * @return array|null
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart, $startTime = 0, $jobsProcessed = 0, $job = null, $lastJobProcessedAt = null)
    {
        return match (true) {
            $this->lostConnection => [static::EXIT_SUCCESS, WorkerStopReason::LostConnection],
            $this->shouldQuit => [static::EXIT_SUCCESS, WorkerStopReason::Interrupted],
            $this->memoryExceeded($options->memory) => [static::$memoryExceededExitCode ?? static::EXIT_MEMORY_LIMIT, WorkerStopReason::MaxMemoryExceeded],
            $this->queueShouldRestart($lastRestart) => [static::EXIT_SUCCESS, WorkerStopReason::ReceivedRestartSignal],
            $options->stopWhenEmpty && is_null($job) => [static::EXIT_SUCCESS, WorkerStopReason::QueueEmpty],
            $options->stopWhenEmptyFor && is_null($job) && $this->currentTime() - ($lastJobProcessedAt ?? $startTime) >= $options->stopWhenEmptyFor => [static::EXIT_SUCCESS, WorkerStopReason::QueueEmptyFor],
            $options->maxTime && $this->currentTime() - $startTime >= $options->maxTime => [static::EXIT_SUCCESS, WorkerStopReason::MaxTimeExceeded],
            $options->maxJobs && $jobsProcessed >= $options->maxJobs => [static::EXIT_SUCCESS, WorkerStopReason::MaxJobsExceeded],
            default => null
        };
    }

    /**
     * Process the next job on the queue.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function runNextJob($connectionName, $queue, WorkerOptions $options)
    {
        $job = $this->getNextJob(
            $this->manager->connection($connectionName), $queue
        );

        // If we're able to pull a job off of the stack, we will process it and then return
        // from this method. If there is no job on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing jobs after sleep.
        if ($job) {
            return $this->runJob($job, $connectionName, $options);
        }

        $this->sleep($options->sleep);
    }

    /**
     * Get the next job from the queue connection.
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $connection
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    protected function getNextJob($connection, $queue)
    {
        $popJobCallback = function ($queue, $index = 0) use ($connection) {
            return $connection->pop($queue, $index);
        };

        $this->raiseBeforeJobPopEvent($connection->getConnectionName(), $queue);

        try {
            if (isset(static::$popCallbacks[$this->name ?? ''])) {
                if (! is_null($job = (static::$popCallbacks[$this->name ?? ''])($popJobCallback, $queue))) {
                    $this->raiseAfterJobPopEvent($connection->getConnectionName(), $job);
                }

                return $job;
            }

            foreach (explode(',', $queue) as $index => $queue) {
                if ($this->queuePaused($connection->getConnectionName(), $queue)) {
                    continue;
                }

                if (! is_null($job = $popJobCallback($queue, $index))) {
                    $this->raiseAfterJobPopEvent($connection->getConnectionName(), $job);

                    return $job;
                }
            }
        } catch (Throwable $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);

            $this->sleep(1);
        }
    }

    /**
     * Determine if a given connection and queue is paused.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @return bool
     */
    protected function queuePaused($connectionName, $queue)
    {
        if (! static::$pausable) {
            return false;
        }

        return $this->cache && $this->manager->isPaused($connectionName, $queue);
    }

    /**
     * Process the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function runJob($job, $connectionName, WorkerOptions $options)
    {
        try {
            return $this->process($connectionName, $job, $options);
        } catch (Throwable $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);
        }
    }

    /**
     * Stop the worker if we have lost connection to a database.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function stopWorkerIfLostConnection($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->lostConnection = true;
        }
    }

    /**
     * Process the given job from the queue.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     *
     * @throws \Throwable
     */
    public function process($connectionName, $job, WorkerOptions $options)
    {
        try {
            // First we will raise the before job event and determine if the job has already run
            // over its maximum attempt limits, which could primarily happen when this job is
            // continually timing out and not actually throwing any exceptions from itself.
            $this->raiseBeforeJobEvent($connectionName, $job);

            $this->markJobAsFailedIfAlreadyExceedsMaxAttempts(
                $connectionName, $job, (int) $options->maxTries
            );

            if ($job->isDeleted()) {
                return $this->raiseAfterJobEvent($connectionName, $job);
            }

            // Here we will fire off the job and let it process. We will catch any exceptions, so
            // they can be reported to the developer's logs, etc. Once the job is finished the
            // proper events will be fired to let any listeners know this job has completed.
            $job->fire();

            $this->raiseAfterJobEvent($connectionName, $job);
        } catch (Throwable $e) {
            $exceptionOccurred = true;

            $this->handleJobException($connectionName, $job, $options, $e);
        } finally {
            $this->events->dispatch(new JobAttempted(
                $connectionName, $job, $exceptionOccurred ?? false
            ));
        }
    }

    /**
     * Handle an exception that occurred while the job was running.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleJobException($connectionName, $job, WorkerOptions $options, Throwable $e)
    {
        try {
            // First, we will go ahead and mark the job as failed if it will exceed the maximum
            // attempts it is allowed to run the next time we process it. If so we will just
            // go ahead and mark it as failed now so we do not have to release this again.
            if (! $job->hasFailed()) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $connectionName, $job, (int) $options->maxTries, $e
                );

                $this->markJobAsFailedIfWillExceedMaxExceptions(
                    $connectionName, $job, $e
                );
            }

            $this->raiseExceptionOccurredJobEvent(
                $connectionName, $job, $e
            );
        } finally {
            // If we catch an exception, we will attempt to release the job back onto the queue
            // so it is not lost entirely. This'll let the job be retried at a later time by
            // another listener (or this same one). We will re-throw this exception after.
            if (! $job->isDeleted() && ! $job->isReleased() && ! $job->hasFailed()) {
                $backoff = $this->calculateBackoff($job, $options);

                $job->release($backoff);

                $this->events->dispatch(new JobReleasedAfterException(
                    $connectionName, $job, $backoff
                ));
            }
        }

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * This will likely be because the job previously exceeded a timeout.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  int  $maxTries
     * @return void
     *
     * @throws \Throwable
     */
    protected function markJobAsFailedIfAlreadyExceedsMaxAttempts($connectionName, $job, $maxTries)
    {
        $maxTries = ! is_null($job->maxTries()) ? $job->maxTries() : $maxTries;

        $retryUntil = $job->retryUntil();

        if ($retryUntil && Carbon::now()->getTimestamp() <= $retryUntil) {
            return;
        }

        if (! $retryUntil && ($maxTries === 0 || $job->attempts() <= $maxTries)) {
            return;
        }

        $this->failJob($job, $e = $this->maxAttemptsExceededException($job));

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  int  $maxTries
     * @param  \Throwable  $e
     * @return void
     */
    protected function markJobAsFailedIfWillExceedMaxAttempts($connectionName, $job, $maxTries, Throwable $e)
    {
        $maxTries = ! is_null($job->maxTries()) ? $job->maxTries() : $maxTries;

        if ($job->retryUntil() && $job->retryUntil() <= Carbon::now()->getTimestamp()) {
            $this->failJob($job, $e);
        }

        if (! $job->retryUntil() && $maxTries > 0 && $job->attempts() >= $maxTries) {
            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function markJobAsFailedIfWillExceedMaxExceptions($connectionName, $job, Throwable $e)
    {
        if (! $this->cache || is_null($uuid = $job->uuid()) ||
            is_null($maxExceptions = $job->maxExceptions())) {
            return;
        }

        if (! $this->cache->get('job-exceptions:'.$uuid)) {
            $this->cache->put('job-exceptions:'.$uuid, 0, Carbon::now()->addDay());
        }

        if ($maxExceptions <= $this->cache->increment('job-exceptions:'.$uuid)) {
            $this->cache->forget('job-exceptions:'.$uuid);

            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed if it should fail on timeouts.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function markJobAsFailedIfItShouldFailOnTimeout($connectionName, $job, Throwable $e)
    {
        if (method_exists($job, 'shouldFailOnTimeout') ? $job->shouldFailOnTimeout() : false) {
            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed and raise the relevant event.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function failJob($job, Throwable $e)
    {
        $job->fail($e);
    }

    /**
     * Calculate the backoff for the given job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    protected function calculateBackoff($job, WorkerOptions $options)
    {
        $backoff = explode(
            ',',
            method_exists($job, 'backoff') && ! is_null($job->backoff())
                ? $job->backoff()
                : $options->backoff
        );

        return (int) ($backoff[$job->attempts() - 1] ?? last($backoff));
    }

    /**
     * Raise an event indicating the worker is starting.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function raiseWorkerStartingEvent($connectionName, $queue, $options)
    {
        $this->events->dispatch(new WorkerStarting($connectionName, $queue, $options));
    }

    /**
     * Raise an event indicating a job is being popped from the queue.
     *
     * @param  string  $connectionName
     * @param  string|null  $queue
     * @return void
     */
    protected function raiseBeforeJobPopEvent($connectionName, $queue = null)
    {
        $this->events->dispatch(new JobPopping($connectionName, $queue));
    }

    /**
     * Raise an event indicating a job has been popped from the queue.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @return void
     */
    protected function raiseAfterJobPopEvent($connectionName, $job)
    {
        $this->events->dispatch(new JobPopped(
            $connectionName, $job
        ));
    }

    /**
     * Raise an event indicating a job is being processed.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseBeforeJobEvent($connectionName, $job)
    {
        $this->events->dispatch(new JobProcessing(
            $connectionName, $job
        ));
    }

    /**
     * Raise an event indicating a job has been processed.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, $job)
    {
        $this->events->dispatch(new JobProcessed(
            $connectionName, $job
        ));
    }

    /**
     * Raise the exception occurred queue job event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent($connectionName, $job, Throwable $e)
    {
        $this->events->dispatch(new JobExceptionOccurred(
            $connectionName, $job, $e
        ));
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param  int|null  $lastRestart
     * @return bool
     */
    protected function queueShouldRestart($lastRestart)
    {
        if (! static::$restartable) {
            return false;
        }

        return $this->getTimestampOfLastQueueRestart() != $lastRestart;
    }

    /**
     * Get the last queue restart timestamp, or null.
     *
     * @return int|null
     */
    protected function getTimestampOfLastQueueRestart()
    {
        if (! static::$restartable) {
            return null;
        }

        if ($this->cache) {
            return $this->cache->get('illuminate:queue:restart');
        }
    }

    /**
     * Enable async signals for the process.
     *
     * @return void
     */
    protected function listenForSignals()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGQUIT, fn () => $this->shouldQuit = true);
        pcntl_signal(SIGTERM, fn () => $this->shouldQuit = true);
        pcntl_signal(SIGINT, fn () => $this->shouldQuit = true);
        pcntl_signal(SIGUSR2, fn () => $this->paused = true);
        pcntl_signal(SIGCONT, fn () => $this->paused = false);
    }

    /**
     * Determine if "async" signals are supported.
     *
     * @return bool
     */
    protected function supportsAsyncSignals()
    {
        return extension_loaded('pcntl');
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int  $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return ((int) $memoryLimit) > 0 && (memory_get_usage(true) / 1024 / 1024) >= ((int) $memoryLimit);
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param  int  $status
     * @param  WorkerOptions|null  $options
     * @param  WorkerStopReason|null  $reason
     * @return int
     */
    public function stop($status = 0, $options = null, $reason = null)
    {
        $this->events->dispatch(new WorkerStopping($status, $options, $reason));

        return $status;
    }

    /**
     * Kill the process.
     *
     * @param  int  $status
     * @param  \Illuminate\Queue\WorkerOptions|null  $options
     * @param  \Illuminate\Queue\WorkerStopReason|null  $reason
     * @return never
     */
    public function kill($status = 0, $options = null, $reason = null)
    {
        $this->events->dispatch(new WorkerStopping($status, $options, $reason));

        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        exit($status);
    }

    /**
     * Create an instance of MaxAttemptsExceededException.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return \Illuminate\Queue\MaxAttemptsExceededException
     */
    protected function maxAttemptsExceededException($job)
    {
        return MaxAttemptsExceededException::forJob($job);
    }

    /**
     * Create an instance of TimeoutExceededException.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return \Illuminate\Queue\TimeoutExceededException
     */
    protected function timeoutExceededException($job)
    {
        return TimeoutExceededException::forJob($job);
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int|float  $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        if ($seconds < 1) {
            usleep($seconds * 1_000_000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Set the cache repository implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return $this
     */
    public function setCache(CacheContract $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Set the name of the worker.
     *
     * @param  string  $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Register a callback to be executed to pick jobs.
     *
     * @param  string  $workerName
     * @param  callable  $callback
     * @return void
     */
    public static function popUsing($workerName, $callback)
    {
        if (is_null($callback)) {
            unset(static::$popCallbacks[$workerName]);
        } else {
            static::$popCallbacks[$workerName] = $callback;
        }
    }

    /**
     * Get the queue manager instance.
     *
     * @return \Illuminate\Contracts\Queue\Factory
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $manager
     * @return void
     */
    public function setManager(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get the current high-resolution timestamp.
     *
     * @return float
     */
    protected function currentTime()
    {
        return hrtime(true) / 1e9;
    }
}
