<?php

namespace Illuminate\Queue\Console;

use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Queue\Events\JobRetryRequested;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'queue:retry')]
class RetryCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'queue:retry
                            {id?* : The ID of the failed job or "all" to retry all jobs}
                            {--queue= : Retry all of the failed jobs for the specified queue}
                            {--range=* : Range of job IDs (numeric) to be retried (e.g. 1-5)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry a failed queue job';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $jobsFound = count($ids = $this->getJobIds()) > 0;

        if ($jobsFound) {
            $this->components->info('Pushing failed queue jobs back onto the queue.');
        }

        foreach ($ids as $id) {
            $job = $this->laravel['queue.failer']->find($id);

            if (is_null($job)) {
                $this->components->error("Unable to find failed job with ID [{$id}].");
            } else {
                $this->laravel['events']->dispatch(new JobRetryRequested($job));

                $this->components->task($id, fn () => $this->retryJob($job));

                $this->laravel['queue.failer']->forget($id);
            }
        }

        $jobsFound ? $this->newLine() : $this->components->info('No retryable jobs found.');
    }

    /**
     * Get the job IDs to be retried.
     *
     * @return array
     */
    protected function getJobIds()
    {
        $ids = (array) $this->argument('id');

        if (count($ids) === 1 && $ids[0] === 'all') {
            $failer = $this->laravel['queue.failer'];

            return method_exists($failer, 'ids')
                ? $failer->ids()
                : Arr::pluck($failer->all(), 'id');
        }

        if ($queue = $this->option('queue')) {
            return $this->getJobIdsByQueue($queue);
        }

        if ($ranges = (array) $this->option('range')) {
            $ids = array_merge($ids, $this->getJobIdsByRanges($ranges));
        }

        return array_values(array_filter(array_unique($ids)));
    }

    /**
     * Get the job IDs by queue, if applicable.
     *
     * @param  string  $queue
     * @return array
     */
    protected function getJobIdsByQueue($queue)
    {
        $failer = $this->laravel['queue.failer'];

        $ids = method_exists($failer, 'ids')
            ? $failer->ids($queue)
            : (new Collection($failer->all()))
                ->where('queue', $queue)
                ->pluck('id')
                ->toArray();

        if (count($ids) === 0) {
            $this->components->error("Unable to find failed jobs for queue [{$queue}].");
        }

        return $ids;
    }

    /**
     * Get the job IDs ranges, if applicable.
     *
     * @param  array  $ranges
     * @return array
     */
    protected function getJobIdsByRanges(array $ranges)
    {
        $ids = [];

        foreach ($ranges as $range) {
            if (preg_match('/^[0-9]+\-[0-9]+$/', $range)) {
                $ids = array_merge($ids, range(...explode('-', $range)));
            }
        }

        return $ids;
    }

    /**
     * Retry the queue job.
     *
     * @param  \stdClass  $job
     * @return void
     */
    protected function retryJob($job)
    {
        $queue = $this->laravel['queue']->connection($job->connection);

        $this->laravel['queue']->connection($job->connection)->pushRaw(
            $this->refreshRetryUntil($this->resetAttempts($job->payload)),
            $job->queue,
            $this->getQueueableOptions($queue, $job)
        );
    }

    /**
     * Reset the payload attempts.
     *
     * Applicable to Redis and other jobs which store attempts in their payload.
     *
     * @param  string  $payload
     * @return string
     */
    protected function resetAttempts($payload)
    {
        $payload = json_decode($payload, true);

        if (isset($payload['attempts'])) {
            $payload['attempts'] = 0;
        }

        return json_encode($payload);
    }

    /**
     * Refresh the "retry until" timestamp for the job.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function refreshRetryUntil($payload)
    {
        $payload = json_decode($payload, true);

        if (! isset($payload['data']['command'])) {
            return json_encode($payload);
        }

        $instance = $this->getInstanceFromPayload($payload);

        if (is_object($instance) && ! $instance instanceof \__PHP_Incomplete_Class && method_exists($instance, 'retryUntil')) {
            $retryUntil = $instance->retryUntil();

            $payload['retryUntil'] = $retryUntil instanceof DateTimeInterface
                ? $retryUntil->getTimestamp()
                : $retryUntil;
        }

        return json_encode($payload);
    }

    /**
     * Get the queueable options from the job.
     *
     * @param  $queue
     * @param  \stdClass  $job
     * @return array
     */
    protected function getQueueableOptions($queue, $job)
    {
        if (! method_exists($queue, 'getQueueableOptions')) {
            return [];
        }

        $payload = json_decode($job->payload, true);

        if (! isset($payload['data']['command'])) {
            return [];
        }

        return $queue->getQueueableOptions($this->getInstanceFromPayload($payload), $job->queue, $job->payload);
    }

    /**
     * Get the job instance from the given payload.
     *
     * @param  array  $payload
     * @return mixed
     *
     * @throws \RuntimeException
     */
    protected function getInstanceFromPayload($payload)
    {
        if (str_starts_with($payload['data']['command'], 'O:')) {
            return unserialize($payload['data']['command']);
        }

        if ($this->laravel->bound(Encrypter::class)) {
            return unserialize($this->laravel->make(Encrypter::class)->decrypt($payload['data']['command']));
        }

        throw new RuntimeException('Unable to extract job payload.');
    }
}
