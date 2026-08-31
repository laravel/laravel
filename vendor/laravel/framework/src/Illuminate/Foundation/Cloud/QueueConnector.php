<?php

namespace Illuminate\Foundation\Cloud;

use Aws\CommandInterface;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Illuminate\Contracts\Database\LostConnectionDetector;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Queue\SqsQueue;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerStopReason;
use Psr\Http\Message\RequestInterface;

class QueueConnector implements ConnectorInterface
{
    /**
     * Reserved memory so that errors can emit events correctly on memory exhaustion.
     */
    private static ?string $reservedMemory = null;

    /**
     * Create a new instance.
     */
    public function __construct(
        protected ConnectorInterface $connector,
        protected Application $app,
    ) {
        //
    }

    /**
     * Establish a queue connection.
     */
    public function connect(array $config): Queue
    {
        $underlying = $this->connector->connect($config['connection']);

        $queue = new Queue(
            $this->app,
            $underlying,
            $this->app[Events::class],
            $config,
        );

        if ($underlying instanceof SqsQueue) {
            $this->registerErrorHandling($underlying->getSqs(), $queue);
        }

        $this->configureQueue($queue);

        if (! $this->app->runningConsoleCommand('queue:work')) {
            return $queue;
        }

        $this->configureWorker($queue);
        $this->configureFailedJobProvider($queue);

        return $queue;
    }

    /**
     * Register SQS client middleware that translates "queue does not exist" errors into ManagedQueueNotFoundExceptions.
     */
    protected function registerErrorHandling(SqsClient $sqs, Queue $queue): void
    {
        $sqs->getHandlerList()->appendSign(function (callable $handler) use ($queue) {
            return function (CommandInterface $command, RequestInterface $request) use ($handler, $queue) {
                return $handler($command, $request)->otherwise(function ($reason) use ($command, $queue) {
                    if ($reason instanceof AwsException &&
                        $reason->getAwsErrorCode() === 'AWS.SimpleQueueService.NonExistentQueue') {
                        $name = $queue->normalizeQueue($command['QueueUrl'] ?? null);

                        throw new ManagedQueueNotFoundException(
                            "Managed queue [{$name}] does not exist.", 0, $reason,
                        );
                    }

                    throw $reason;
                });
            };
        }, 'managed-queue-not-found');
    }

    /**
     * Configure the queue.
     */
    protected function configureQueue(Queue $queue): void
    {
        $this->app['events']->listen(fn (JobQueued $event) => $event->connectionName === $queue->getConnectionName()
            ? $queue->finishQueueingJob($event->queue)
            : null);
    }

    /**
     * Configure the queue worker.
     */
    protected function configureWorker(Queue $queue): void
    {
        Worker::$restartable = false;
        Worker::$pausable = false;

        // Exit the worker (and restart the pod) when the agent socket is unreachable...
        $this->app->extend(
            LostConnectionDetector::class,
            fn ($detector) => new AgentAwareLostConnectionDetector($detector),
        );

        $this->app['events']->listen(fn (WorkerStopping $event) => match ($event->reason) {
            WorkerStopReason::TimedOut => $queue->finishProcessingJob(default: 'released'),
            default => $queue->finishProcessingJob(),
        });

        static::$reservedMemory = str_repeat('x', 32768);

        register_shutdown_function(function () use ($queue) {
            static::$reservedMemory = null;

            if (! is_null($error = error_get_last()) && in_array($error['type'], [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE])) {
                $queue->finishProcessingJob(default: 'released');
            }
        });
    }

    /**
     * Configure the failed job provider.
     */
    protected function configureFailedJobProvider(Queue $queue): void
    {
        $this->app['queue.failer']->setQueue($queue);
    }
}
