<?php

namespace Illuminate\Bus;

use Closure;
use Illuminate\Bus\Events\BatchDispatched;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Laravel\SerializableClosure\SerializableClosure;
use RuntimeException;
use Throwable;
use UnitEnum;

use function Illuminate\Support\enum_value;

class PendingBatch
{
    use Conditionable;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The batch name.
     *
     * @var string
     */
    public $name = '';

    /**
     * The jobs that belong to the batch.
     *
     * @var \Illuminate\Support\Collection
     */
    public $jobs;

    /**
     * The batch options.
     *
     * @var array
     */
    public $options = [];

    /**
     * Jobs that have been verified to contain the Batchable trait.
     *
     * @var array<class-string, bool>
     */
    protected static $batchableClasses = [];

    /**
     * Create a new pending batch instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  \Illuminate\Support\Collection  $jobs
     */
    public function __construct(Container $container, Collection $jobs)
    {
        $this->container = $container;

        $this->jobs = $jobs->filter()->values()->each(function (object|array $job) {
            $this->ensureJobIsBatchable($job);
        });
    }

    /**
     * Add jobs to the batch.
     *
     * @param  iterable|object|array  $jobs
     * @return $this
     */
    public function add($jobs)
    {
        $jobs = is_iterable($jobs) ? $jobs : Arr::wrap($jobs);

        foreach ($jobs as $job) {
            $this->ensureJobIsBatchable($job);

            $this->jobs->push($job);
        }

        return $this;
    }

    /**
     * Ensure the given job is batchable.
     *
     * @param  object|array  $job
     * @return void
     */
    protected function ensureJobIsBatchable(object|array $job): void
    {
        foreach (Arr::wrap($job) as $job) {
            if ($job instanceof PendingBatch || $job instanceof Closure) {
                return;
            }

            if (! (static::$batchableClasses[$job::class] ?? false) && ! in_array(Batchable::class, class_uses_recursive($job))) {
                static::$batchableClasses[$job::class] = false;

                throw new RuntimeException(sprintf('Attempted to batch job [%s], but it does not use the Batchable trait.', $job::class));
            }

            static::$batchableClasses[$job::class] = true;
        }
    }

    /**
     * Add a callback to be executed when the batch is stored.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function before($callback)
    {
        $this->registerCallback('before', $callback);

        return $this;
    }

    /**
     * Get the "before" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function beforeCallbacks()
    {
        return $this->options['before'] ?? [];
    }

    /**
     * Add a callback to be executed after a job in the batch have executed successfully.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function progress($callback)
    {
        $this->registerCallback('progress', $callback);

        return $this;
    }

    /**
     * Get the "progress" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function progressCallbacks()
    {
        return $this->options['progress'] ?? [];
    }

    /**
     * Add a callback to be executed after all jobs in the batch have executed successfully.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function then($callback)
    {
        $this->registerCallback('then', $callback);

        return $this;
    }

    /**
     * Get the "then" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function thenCallbacks()
    {
        return $this->options['then'] ?? [];
    }

    /**
     * Add a callback to be executed after the first failing job in the batch.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function catch($callback)
    {
        $this->registerCallback('catch', $callback);

        return $this;
    }

    /**
     * Get the "catch" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function catchCallbacks()
    {
        return $this->options['catch'] ?? [];
    }

    /**
     * Add a callback to be executed after the batch has finished executing.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function finally($callback)
    {
        $this->registerCallback('finally', $callback);

        return $this;
    }

    /**
     * Get the "finally" callbacks that have been registered with the pending batch.
     *
     * @return array
     */
    public function finallyCallbacks()
    {
        return $this->options['finally'] ?? [];
    }

    /**
     * Indicate that the batch should not be canceled when a job within the batch fails.
     *
     * Optionally, add callbacks to be executed upon each job failure.
     *
     * @phpstan-type TParam (Closure(\Illuminate\Bus\Batch, \Throwable|null): mixed)|(callable(\Illuminate\Bus\Batch, \Throwable|null): mixed)
     *
     * @param  bool|TParam|array<array-key, TParam>  $param
     * @return $this
     */
    public function allowFailures($param = true)
    {
        if (! is_bool($param)) {
            $param = Arr::wrap($param);

            foreach ($param as $callback) {
                if (is_callable($callback)) {
                    $this->registerCallback('failure', $callback);
                }
            }
        }

        $this->options['allowFailures'] = ! ($param === false);

        return $this;
    }

    /**
     * Determine if the pending batch allows jobs to fail without cancelling the batch.
     *
     * @return bool
     */
    public function allowsFailures()
    {
        return Arr::get($this->options, 'allowFailures', false) === true;
    }

    /**
     * Get the "failure" callbacks that have been registered with the pending batch.
     *
     * @return array<array-key, Closure|callable>
     */
    public function failureCallbacks(): array
    {
        return $this->options['failure'] ?? [];
    }

    /**
     * Register a callback with proper serialization.
     */
    private function registerCallback(string $type, Closure|callable $callback): void
    {
        $this->options[$type][] = $callback instanceof Closure
            ? new SerializableClosure($callback)
            : $callback;
    }

    /**
     * Set the name for the batch.
     *
     * @param  string  $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Specify the queue connection that the batched jobs should run on.
     *
     * @param  \UnitEnum|string  $connection
     * @return $this
     */
    public function onConnection(UnitEnum|string $connection)
    {
        $this->options['connection'] = enum_value($connection);

        return $this;
    }

    /**
     * Get the connection used by the pending batch.
     *
     * @return string|null
     */
    public function connection()
    {
        return $this->options['connection'] ?? null;
    }

    /**
     * Specify the queue that the batched jobs should run on.
     *
     * @param  \UnitEnum|string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->options['queue'] = enum_value($queue);

        return $this;
    }

    /**
     * Get the queue used by the pending batch.
     *
     * @return string|null
     */
    public function queue()
    {
        return $this->options['queue'] ?? null;
    }

    /**
     * Add additional data into the batch's options array.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function withOption(string $key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Dispatch the batch.
     *
     * @return \Illuminate\Bus\Batch
     *
     * @throws \Throwable
     */
    public function dispatch()
    {
        $repository = $this->container->make(BatchRepository::class);

        try {
            $batch = $this->store($repository);

            $batch = $batch->add($this->jobs);
        } catch (Throwable $e) {
            if (isset($batch)) {
                $repository->delete($batch->id);
            }

            throw $e;
        }

        $this->container->make(EventDispatcher::class)->dispatch(
            new BatchDispatched($batch)
        );

        return $batch;
    }

    /**
     * Dispatch the batch after the response is sent to the browser.
     *
     * @return \Illuminate\Bus\Batch
     */
    public function dispatchAfterResponse()
    {
        $repository = $this->container->make(BatchRepository::class);

        $batch = $this->store($repository);

        if ($batch) {
            $this->container->terminating(function () use ($batch) {
                $this->dispatchExistingBatch($batch);
            });
        }

        return $batch;
    }

    /**
     * Dispatch an existing batch.
     *
     * @param  \Illuminate\Bus\Batch  $batch
     * @return void
     *
     * @throws \Throwable
     */
    protected function dispatchExistingBatch($batch)
    {
        try {
            $batch = $batch->add($this->jobs);
        } catch (Throwable $e) {
            $batch->delete();

            throw $e;
        }

        $this->container->make(EventDispatcher::class)->dispatch(
            new BatchDispatched($batch)
        );
    }

    /**
     * Dispatch the batch if the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @return \Illuminate\Bus\Batch|null
     */
    public function dispatchIf($boolean)
    {
        return value($boolean) ? $this->dispatch() : null;
    }

    /**
     * Dispatch the batch unless the given truth test passes.
     *
     * @param  bool|\Closure  $boolean
     * @return \Illuminate\Bus\Batch|null
     */
    public function dispatchUnless($boolean)
    {
        return ! value($boolean) ? $this->dispatch() : null;
    }

    /**
     * Store the batch using the given repository.
     *
     * @param  \Illuminate\Bus\BatchRepository  $repository
     * @return \Illuminate\Bus\Batch
     */
    protected function store($repository)
    {
        $batch = $repository->store($this);

        (new Collection($this->beforeCallbacks()))->each(function ($handler) use ($batch) {
            try {
                return $handler($batch);
            } catch (Throwable $e) {
                if (function_exists('report')) {
                    report($e);
                }
            }
        });

        return $batch;
    }
}
