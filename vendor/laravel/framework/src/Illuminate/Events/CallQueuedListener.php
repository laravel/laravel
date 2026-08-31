<?php

namespace Illuminate\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallQueuedListener implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * The listener class name.
     *
     * @var class-string
     */
    public $class;

    /**
     * The listener method.
     *
     * @var string
     */
    public $method;

    /**
     * The data to be passed to the listener.
     *
     * @var array
     */
    public $data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The maximum number of exceptions allowed, regardless of attempts.
     *
     * @var int
     */
    public $maxExceptions;

    /**
     * The number of seconds to wait before retrying a job that encountered an uncaught exception.
     *
     * @var int
     */
    public $backoff;

    /**
     * The timestamp indicating when the job should timeout.
     *
     * @var int
     */
    public $retryUntil;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout;

    /**
     * Indicates if the job should fail if the timeout is exceeded.
     *
     * @var bool
     */
    public $failOnTimeout = false;

    /**
     * Indicates if the job should be encrypted.
     *
     * @var bool
     */
    public $shouldBeEncrypted = false;

    /**
     * Indicates if the listener should be unique.
     */
    public bool $shouldBeUnique = false;

    /**
     * Indicates if the listener should be unique until processing begins.
     */
    public bool $shouldBeUniqueUntilProcessing = false;

    /**
     * The unique ID of the listener.
     */
    public mixed $uniqueId = null;

    /**
     * The number of seconds the unique lock should be maintained.
     */
    public ?int $uniqueFor = null;

    /**
     * Create a new job instance.
     *
     * @param  class-string  $class
     * @param  string  $method
     * @param  array  $data
     */
    public function __construct($class, $method, $data)
    {
        $this->data = $data;
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * Handle the queued job.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function handle(Container $container)
    {
        $this->prepareData();

        $handler = $this->setJobInstanceIfNecessary(
            $this->job, $container->make($this->class)
        );

        $handler->{$this->method}(...array_values($this->data));
    }

    /**
     * Determine if the listener should be unique.
     */
    public function shouldBeUnique(): bool
    {
        return $this->shouldBeUnique;
    }

    /**
     * Determine if the listener should be unique until processing begins.
     */
    public function shouldBeUniqueUntilProcessing(): bool
    {
        return $this->shouldBeUniqueUntilProcessing;
    }

    /**
     * Get the unique ID for the listener.
     */
    public function uniqueId(): mixed
    {
        return $this->uniqueId;
    }

    /**
     * Get the number of seconds the unique lock should be maintained.
     */
    public function uniqueFor(): ?int
    {
        return $this->uniqueFor;
    }

    /**
     * Get the cache store used to manage unique locks.
     */
    public function uniqueVia(): ?Cache
    {
        $listener = Container::getInstance()->make($this->class);

        if (! method_exists($listener, 'uniqueVia')) {
            return null;
        }

        $this->prepareData();

        return $listener->uniqueVia(...array_values($this->data));
    }

    /**
     * Set the job instance of the given class if necessary.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  object  $instance
     * @return object
     */
    protected function setJobInstanceIfNecessary(Job $job, $instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive($instance))) {
            $instance->setJob($job);
        }

        return $instance;
    }

    /**
     * Call the failed method on the job instance.
     *
     * The event instance and the exception will be passed.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed($e)
    {
        $this->prepareData();

        $handler = Container::getInstance()->make($this->class);

        $parameters = array_merge(array_values($this->data), [$e]);

        if (method_exists($handler, 'failed')) {
            $handler->failed(...$parameters);
        }
    }

    /**
     * Unserialize the data if needed.
     *
     * @return void
     */
    protected function prepareData()
    {
        if (is_string($this->data)) {
            $this->data = unserialize($this->data);
        }
    }

    /**
     * Get the display name for the queued job.
     *
     * @return string
     */
    public function displayName()
    {
        return $this->class;
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->data = array_map(function ($data) {
            return is_object($data) ? clone $data : $data;
        }, $this->data);
    }
}
