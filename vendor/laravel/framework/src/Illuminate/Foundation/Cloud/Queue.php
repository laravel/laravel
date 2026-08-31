<?php

namespace Illuminate\Foundation\Cloud;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Symfony\Component\Console\Input\ArgvInput;

class Queue implements QueueContract, ClearableQueue
{
    use ForwardsCalls;

    /**
     * The currently processing job.
     *
     * @var \Illuminate\Contracts\Queue\Job|null
     */
    protected $processingJob = null;

    /**
     * The queue for the currently processing job.
     *
     * @var string|null
     */
    protected $processingQueue = null;

    /**
     * The date the last job started processing.
     *
     * @var \Carbon\CarbonImmutable
     */
    protected $processingJobStartedAt = null;

    /**
     * Create a new Queue instance.
     */
    public function __construct(
        protected Application $app,
        protected QueueContract $queue,
        protected Events $events,
        protected array $config,
    ) {
        //
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return $this->queue->size(...func_get_args());
    }

    /**
     * Get the number of pending jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function pendingSize($queue = null)
    {
        return $this->queue->pendingSize(...func_get_args());
    }

    /**
     * Get the number of delayed jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function delayedSize($queue = null)
    {
        return $this->queue->delayedSize(...func_get_args());
    }

    /**
     * Get the number of reserved jobs.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function reservedSize($queue = null)
    {
        return $this->queue->reservedSize(...func_get_args());
    }

    /**
     * Get the creation timestamp of the oldest pending job, excluding delayed jobs.
     *
     * @param  string|null  $queue
     * @return int|null
     */
    public function creationTimeOfOldestPendingJob($queue = null)
    {
        return $this->queue->creationTimeOfOldestPendingJob(...func_get_args());
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string|object  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        return $this->queue->push(...func_get_args());
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string  $queue
     * @param  string|object  $job
     * @param  mixed  $data
     * @return mixed
     */
    public function pushOn($queue, $job, $data = '')
    {
        return $this->queue->pushOn(...func_get_args());
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $result = $this->queue->pushRaw(...func_get_args());

        $this->finishQueueingJob($queue);

        return $result;
    }

    /**
     * Push a new job onto the queue after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string|object  $job
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->queue->later(...func_get_args());
    }

    /**
     * Push a new job onto a specific queue after (n) seconds.
     *
     * @param  string  $queue
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string|object  $job
     * @param  mixed  $data
     * @return mixed
     */
    public function laterOn($queue, $delay, $job, $data = '')
    {
        return $this->queue->laterOn(...func_get_args());
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
        return $this->queue->bulk(...func_get_args());
    }

    /**
     * Pop the next job off of the queue.
     *
     * Jobs come straight from SQS unless the cloud-agent is enabled.
     *
     * When cloud-agent is enabled, we long-poll the agent's runtime socket instead.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        $this->finishProcessingJob();

        $job = $this->usesAgent($queue)
            ? $this->popFromAgent()
            : $this->queue->pop(...func_get_args());

        $this->startProcessingJob($queue, $job);

        return $job;
    }

    /**
     * Long-poll the cloud-agent's runtime socket and wrap the next message in a CloudJob.
     *
     * @return \Illuminate\Foundation\Cloud\CloudJob|null
     */
    protected function popFromAgent()
    {
        $data = $this->requestNextJobFromAgent();

        if (! (is_array($data) && is_string($messageId = $data['messageId'] ?? null) && $messageId !== '')) {
            return null;
        }

        // Coerce a non-string handle to null so a malformed response degrades gracefully...
        $receiptHandle = is_string($handle = $data['receiptHandle'] ?? null) ? $handle : null;

        return new CloudJob(
            $this->queue->getContainer(),
            $this->queue->getSqs(),
            [
                'MessageId' => $messageId,
                'ReceiptHandle' => $receiptHandle,
                'Body' => is_string($body = $data['body'] ?? null) ? $body : '',
                'Attributes' => $data['attributes'] ?? [],
            ],
            $this->queue->getConnectionName(),
            $data['queueUrl'] ?? null,
            fn (string $status, ?int $delay) => $this->reportJobStatusToAgent(
                $messageId, $receiptHandle, $status, $delay
            ),
        );
    }

    /**
     * Long-poll the agent's runtime socket (GET /next) for the next job.
     *
     * @throws \Illuminate\Foundation\Cloud\AgentUnreachableException
     */
    protected function requestNextJobFromAgent(): ?array
    {
        try {
            $response = $this->agentRequest()
                ->timeout(65)
                ->get('/next');
        } catch (ConnectionException $e) {
            throw new AgentUnreachableException(
                'The Laravel Cloud agent runtime socket is unreachable.', previous: $e
            );
        }

        if ($response->status() === 204) {
            return null;
        }

        if (! $response->ok()) {
            throw new AgentUnreachableException(
                "The Laravel Cloud agent returned HTTP {$response->status()} from GET /next."
            );
        }

        if (! is_array($data = $response->json())) {
            throw new AgentUnreachableException(
                'The Laravel Cloud agent returned a non-array body from GET /next.'
            );
        }

        return $data;
    }

    /**
     * Report a job's terminal outcome back to the agent (POST /result).
     *
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Foundation\Cloud\AgentUnreachableException
     */
    protected function reportJobStatusToAgent(string $messageId, ?string $receiptHandle, string $status, ?int $delay = null): void
    {
        try {
            $this->agentRequest()
                ->timeout(10)
                ->throw()
                ->retry(3, 100, fn ($exception) => $exception instanceof ConnectionException)
                ->post('/result', array_filter([
                    'messageId' => $messageId,
                    'receiptHandle' => $receiptHandle,
                    'status' => $status,
                    'delay' => $delay,
                ], fn ($value) => $value !== null));
        } catch (ConnectionException $e) {
            throw new AgentUnreachableException(
                'The Laravel Cloud agent runtime socket is unreachable.', previous: $e
            );
        } catch (RequestException $e) {
            if ($e->response->serverError()) {
                throw new AgentUnreachableException(
                    "The Laravel Cloud agent returned HTTP {$e->response->status()} from POST /result.", previous: $e
                );
            }

            throw $e;
        }
    }

    /**
     * Get a pending HTTP request bound to the agent's Unix runtime socket.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function agentRequest()
    {
        return Http::baseUrl('http://localhost')->withOptions([
            'curl' => [
                CURLOPT_UNIX_SOCKET_PATH => $this->config['agent']['socket'] ?? '/tmp/cloud-agent.sock',
            ],
        ]);
    }

    /**
     * Delete all of the jobs from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue)
    {
        return $this->queue->clear(...func_get_args());
    }

    /**
     * Determine whether the next job should be received from the in-container cloud-agent.
     *
     * @param  string|null  $queue
     */
    protected function usesAgent($queue = null): bool
    {
        return ($this->config['agent']['enabled'] ?? false)
            && $this->app->runningConsoleCommand('queue:work')
            && $this->queue->getQueue($queue) === $this->queue->getQueue($this->workerQueue());
    }

    /**
     * Get the queue the running queue:work worker is processing.
     *
     * @return string
     */
    protected function workerQueue()
    {
        return (new ArgvInput)->getParameterOption('--queue')
            ?: ($this->config['queue'] ?? 'default');
    }

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->queue->getConnectionName();
    }

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name)
    {
        $this->queue->setConnectionName(...func_get_args());

        return $this;
    }

    /**
     * Set the queue configuration array.
     *
     * @param  array  $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;

        $this->queue->setConfig($config['connection']);

        return $this;
    }

    /**
     * Get the queueable options from the job.
     *
     * @param  mixed  $job
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return array{DelaySeconds?: int, MessageGroupId?: string, MessageDeduplicationId?: string}
     */
    public function getQueueableOptions($job, $queue, $payload, $delay = null): array
    {
        if (! method_exists($this->queue, 'getQueueableOptions')) {
            return [];
        }

        return $this->queue->getQueueableOptions(...func_get_args());
    }

    /**
     * Finish processing the current job and emit a queue event.
     *
     * @param  string  $default
     * @param  \Carbon\CarbonImmutable|null  $timestamp
     * @return void
     */
    public function finishProcessingJob($default = 'processed', $timestamp = null)
    {
        if (! $this->processingJob) {
            return;
        }

        $timestamp ??= CarbonImmutable::now('UTC');

        $this->events->emit([
            '_cloud_event' => 'queue',
            'timestamp' => $timestamp->toDateTimeString('microsecond'),
            'type' => match (true) {
                $this->processingJob->hasFailed() => 'failed',
                $this->processingJob->isReleased() => 'released',
                default => $default,
            },
            'queue' => $this->processingQueue,
            'duration_ms' => (int) $this->processingJobStartedAt->diffInMilliseconds($timestamp),
        ]);

        $this->processingQueue
            = $this->processingJob
            = $this->processingJobStartedAt
            = null;
    }

    /**
     * Last job details resolver.
     *
     * @return array{queue: string, attempts: int, started_at: CarbonImmutable}
     */
    public function processingJobDetails()
    {
        return [
            'queue' => $this->processingQueue,
            'attempts' => $this->processingJob->attempts(),
            'started_at' => $this->processingJobStartedAt,
        ];
    }

    /**
     * Handle jobs finishing being queued.
     *
     * @param  string  $queue
     */
    public function finishQueueingJob($queue)
    {
        $this->events->emit([
            '_cloud_event' => 'queue',
            'timestamp' => CarbonImmutable::now('UTC')->toDateTimeString('microsecond'),
            'type' => 'queued',
            'queue' => $this->normalizeQueue($queue),
        ]);
    }

    /**
     * Handle a job being popped.
     *
     * @param  string|null  $queue
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @return void
     */
    protected function startProcessingJob($queue, $job)
    {
        if (! $job) {
            return;
        }

        $this->processingJob = $job;
        $this->processingQueue = $this->normalizeQueue($queue);
        $this->processingJobStartedAt = CarbonImmutable::now('UTC');

        $this->events->emit([
            '_cloud_event' => 'queue',
            'timestamp' => $this->processingJobStartedAt->toDateTimeString('microsecond'),
            'type' => 'started',
            'queue' => $this->processingQueue,
        ]);
    }

    /**
     * Normalize the queue name.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function normalizeQueue($queue)
    {
        $prefix = $this->config['connection']['prefix'] ?? null;
        $suffix = $this->config['connection']['suffix'] ?? null;

        return Str::of($this->queue->getQueue($queue))
            ->when($prefix, fn ($str) => $str->chopStart($prefix.'/'))
            ->when($suffix, fn ($str) => $str->endsWith('.fifo')
                ? $str->chopEnd('.fifo')->chopEnd($suffix)->append('.fifo')
                : $str->chopEnd($suffix))
            ->toString();
    }

    /**
     * Dynamically pass method calls to the underlying queue.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->queue, $method, $parameters);
    }
}
