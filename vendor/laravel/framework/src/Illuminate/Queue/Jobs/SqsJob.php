<?php

namespace Illuminate\Queue\Jobs;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;

class SqsJob extends Job implements JobContract
{
    /**
     * The Amazon SQS client instance.
     *
     * @var \Aws\Sqs\SqsClient
     */
    protected $sqs;

    /**
     * The Amazon SQS job instance.
     *
     * @var array
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Aws\Sqs\SqsClient  $sqs
     * @param  array  $job
     * @param  string  $connectionName
     * @param  string  $queue
     */
    public function __construct(Container $container, SqsClient $sqs, array $job, $connectionName, $queue)
    {
        $this->sqs = $sqs;
        $this->job = $job;
        $this->queue = $queue;
        $this->container = $container;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the job back into the queue after (n) seconds.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $this->changeMessageVisibilityInSqs($delay);
    }

    /**
     * Reset the SQS message's visibility timeout so it becomes available again.
     *
     * @param  int  $delay
     * @return void
     */
    protected function changeMessageVisibilityInSqs($delay)
    {
        $this->sqs->changeMessageVisibility([
            'QueueUrl' => $this->queue,
            'ReceiptHandle' => $this->job['ReceiptHandle'],
            'VisibilityTimeout' => $delay,
        ]);
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->deleteMessageFromSqs();
    }

    /**
     * Delete the message from the SQS queue.
     *
     * @return void
     */
    protected function deleteMessageFromSqs()
    {
        $this->sqs->deleteMessage([
            'QueueUrl' => $this->queue, 'ReceiptHandle' => $this->job['ReceiptHandle'],
        ]);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->job['Attributes']['ApproximateReceiveCount'];
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job['MessageId'];
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->job['Body'];
    }

    /**
     * Get the underlying SQS client instance.
     *
     * @return \Aws\Sqs\SqsClient
     */
    public function getSqs()
    {
        return $this->sqs;
    }

    /**
     * Get the underlying raw SQS job.
     *
     * @return array
     */
    public function getSqsJob()
    {
        return $this->job;
    }
}
