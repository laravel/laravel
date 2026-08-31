<?php

namespace Illuminate\Queue;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\UniqueLock;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Log\Context\Repository as ContextRepository;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use ReflectionClass;
use RuntimeException;

class CallQueuedHandler
{
    /**
     * The bus dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $dispatcher;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new handler instance.
     *
     * @param  \Illuminate\Contracts\Bus\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Dispatcher $dispatcher, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the queued job.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  array  $data
     * @return void
     */
    public function call(Job $job, array $data)
    {
        try {
            $command = $this->setJobInstanceIfNecessary(
                $job, $this->getCommand($data)
            );
        } catch (ModelNotFoundException $e) {
            return $this->handleModelNotFound($job, $e);
        }

        $this->dispatchThroughMiddleware($job, $command);

        if (! $job->isReleased() && ! $this->commandShouldBeUniqueUntilProcessing($command)) {
            $this->ensureUniqueJobLockIsReleased($command);
        }

        if (! $job->hasFailed() && ! $job->isReleased()) {
            $this->ensureNextJobInChainIsDispatched($command);
            $this->ensureSuccessfulBatchJobIsRecorded($command);
        }

        if (! $job->isDeletedOrReleased()) {
            $job->delete();
        }
    }

    /**
     * Get the command from the given payload.
     *
     * @param  array  $data
     * @return mixed
     *
     * @throws \RuntimeException
     */
    protected function getCommand(array $data)
    {
        if (str_starts_with($data['command'], 'O:')) {
            return unserialize($data['command']);
        }

        if ($this->container->bound(Encrypter::class)) {
            return unserialize($this->container[Encrypter::class]->decrypt($data['command']));
        }

        throw new RuntimeException('Unable to extract job payload.');
    }

    /**
     * Dispatch the given job / command through its specified middleware.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  mixed  $command
     * @return mixed
     */
    protected function dispatchThroughMiddleware(Job $job, $command)
    {
        if ($command instanceof \__PHP_Incomplete_Class) {
            throw new Exception('Job is incomplete class: '.json_encode($command));
        }

        $lockReleased = false;

        return (new Pipeline($this->container))->send($command)
            ->through(array_merge(method_exists($command, 'middleware') ? $command->middleware() : [], $command->middleware ?? []))
            ->finally(function ($command) use (&$lockReleased) {
                if (! $lockReleased && $this->commandShouldBeUniqueUntilProcessing($command) && ! $command->job->isReleased()) {
                    $this->ensureUniqueJobLockIsReleased($command);
                }
            })
            ->then(function ($command) use ($job, &$lockReleased) {
                if ($this->commandShouldBeUniqueUntilProcessing($command)) {
                    $this->ensureUniqueJobLockIsReleased($command);

                    $lockReleased = true;
                }

                return $this->dispatcher->dispatchNow(
                    $command, $this->resolveHandler($job, $command)
                );
            });
    }

    /**
     * Resolve the handler for the given command.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  mixed  $command
     * @return mixed
     */
    protected function resolveHandler($job, $command)
    {
        $handler = $this->dispatcher->getCommandHandler($command) ?: null;

        if ($handler) {
            $this->setJobInstanceIfNecessary($job, $handler);
        }

        return $handler;
    }

    /**
     * Set the job instance of the given class if necessary.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  mixed  $instance
     * @return mixed
     */
    protected function setJobInstanceIfNecessary(Job $job, $instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive($instance))) {
            $instance->setJob($job);
        }

        return $instance;
    }

    /**
     * Ensure the next job in the chain is dispatched if applicable.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureNextJobInChainIsDispatched($command)
    {
        if (method_exists($command, 'dispatchNextJobInChain')) {
            $command->dispatchNextJobInChain();
        }
    }

    /**
     * Ensure the batch is notified of the successful job completion.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureSuccessfulBatchJobIsRecorded($command)
    {
        $uses = class_uses_recursive($command);

        if (! in_array(Batchable::class, $uses) ||
            ! in_array(InteractsWithQueue::class, $uses)) {
            return;
        }

        if ($batch = $command->batch()) {
            $batch->recordSuccessfulJob($command->job->uuid());
        }
    }

    /**
     * Ensure the lock for a unique job is released.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureUniqueJobLockIsReleased($command)
    {
        if ($this->commandShouldBeUnique($command)) {
            (new UniqueLock($this->container->make(Cache::class)))->release($command);
        }
    }

    /**
     * Determine if the given command should be unique.
     */
    protected function commandShouldBeUnique(mixed $command): bool
    {
        return $command instanceof ShouldBeUnique ||
            ($command instanceof CallQueuedListener && $command->shouldBeUnique());
    }

    /**
     * Determine if the given command should be unique until processing begins.
     */
    protected function commandShouldBeUniqueUntilProcessing(mixed $command): bool
    {
        return $command instanceof ShouldBeUniqueUntilProcessing ||
            ($command instanceof CallQueuedListener && $command->shouldBeUniqueUntilProcessing());
    }

    /**
     * Handle a model not found exception.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Throwable  $e
     * @return void
     */
    protected function handleModelNotFound(Job $job, $e)
    {
        $class = $job->resolveQueuedJobClass();

        try {
            $reflectionClass = new ReflectionClass($class);

            $shouldDelete = $reflectionClass->getDefaultProperties()['deleteWhenMissingModels']
                ?? count($reflectionClass->getAttributes(DeleteWhenMissingModels::class)) !== 0;
        } catch (Exception) {
            $shouldDelete = false;
        }

        $this->ensureUniqueJobLockIsReleasedViaContext();

        if ($shouldDelete) {
            $this->ensureSuccessfulBatchJobIsRecordedForMissingModel($job, $class);

            return $job->delete();
        }

        return $job->fail($e);
    }

    /**
     * Ensure the lock for a unique job is released via context.
     *
     * This is required when we can't unserialize the job due to missing models.
     *
     * @return void
     */
    protected function ensureUniqueJobLockIsReleasedViaContext()
    {
        if (! $this->container->bound(ContextRepository::class) ||
            ! $this->container->bound(CacheFactory::class)) {
            return;
        }

        $context = $this->container->make(ContextRepository::class);

        [$store, $key] = [
            $context->getHidden('laravel_unique_job_cache_store'),
            $context->getHidden('laravel_unique_job_key'),
        ];

        if ($store && $key) {
            $this->container->make(CacheFactory::class)
                ->store($store)
                ->lock($key)
                ->forceRelease();
        }
    }

    /**
     * Record a potentially batched job as successful when deleted because models were missing.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $class
     * @return void
     */
    protected function ensureSuccessfulBatchJobIsRecordedForMissingModel(Job $job, string $class)
    {
        if (! in_array(Batchable::class, class_uses_recursive($class), true)) {
            return;
        }

        if (! $this->container->bound(BatchRepository::class)) {
            return;
        }

        $batchId = $job->payload()['data']['batchId'] ?? null;

        if ((! is_string($batchId) || $batchId === '') ||
             ! is_string($job->uuid()) || $job->uuid() === '') {
            return;
        }

        if ($batch = $this->container->make(BatchRepository::class)->find($batchId)) {
            $batch->recordSuccessfulJob($job->uuid());
        }
    }

    /**
     * Call the failed method on the job instance.
     *
     * The exception that caused the failure will be passed.
     *
     * @param  array  $data
     * @param  \Throwable|null  $e
     * @param  string  $uuid
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @return void
     */
    public function failed(array $data, $e, string $uuid, ?Job $job = null)
    {
        $command = $this->getCommand($data);

        if (! is_null($job)) {
            $command = $this->setJobInstanceIfNecessary($job, $command);
        }

        if (! $this->commandShouldBeUniqueUntilProcessing($command)) {
            $this->ensureUniqueJobLockIsReleased($command);
        }

        if ($command instanceof \__PHP_Incomplete_Class) {
            return;
        }

        $this->ensureFailedBatchJobIsRecorded($uuid, $command, $e);
        $this->ensureChainCatchCallbacksAreInvoked($uuid, $command, $e);

        if (method_exists($command, 'failed')) {
            $command->failed($e);
        }
    }

    /**
     * Ensure the batch is notified of the failed job.
     *
     * @param  string  $uuid
     * @param  mixed  $command
     * @param  \Throwable  $e
     * @return void
     */
    protected function ensureFailedBatchJobIsRecorded(string $uuid, $command, $e)
    {
        if (! in_array(Batchable::class, class_uses_recursive($command))) {
            return;
        }

        if ($batch = $command->batch()) {
            $batch->recordFailedJob($uuid, $e);
        }
    }

    /**
     * Ensure the chained job catch callbacks are invoked.
     *
     * @param  string  $uuid
     * @param  mixed  $command
     * @param  \Throwable  $e
     * @return void
     */
    protected function ensureChainCatchCallbacksAreInvoked(string $uuid, $command, $e)
    {
        if (method_exists($command, 'invokeChainCatchCallbacks')) {
            $command->invokeChainCatchCallbacks($e);
        }
    }
}
