<?php

namespace Illuminate\Queue;

use DateTimeInterface;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\FakeJob;
use Illuminate\Support\InteractsWithTime;
use InvalidArgumentException;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;
use Throwable;

trait InteractsWithQueue
{
    use InteractsWithTime;

    /**
     * The underlying queue job instance.
     *
     * @var \Illuminate\Contracts\Queue\Job|null
     */
    public $job;

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->job ? $this->job->attempts() : 1;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        if ($this->job) {
            return $this->job->delete();
        }
    }

    /**
     * Fail the job from the queue.
     *
     * @param  \Throwable|string|null  $exception
     * @return void
     */
    public function fail($exception = null)
    {
        if (is_string($exception)) {
            $exception = new ManuallyFailedException($exception);
        }

        if ($exception instanceof Throwable || is_null($exception)) {
            if ($this->job) {
                return $this->job->fail($exception);
            }
        } else {
            throw new InvalidArgumentException('The fail method requires a string or an instance of Throwable.');
        }
    }

    /**
     * Release the job back into the queue after (n) seconds.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        $delay = $delay instanceof DateTimeInterface
            ? $this->secondsUntil($delay)
            : $delay;

        if ($this->job) {
            return $this->job->release($delay);
        }
    }

    /**
     * Indicate that queue interactions like fail, delete, and release should be faked.
     *
     * @return $this
     */
    public function withFakeQueueInteractions()
    {
        $this->job = new FakeJob;

        return $this;
    }

    /**
     * Assert that the job was deleted from the queue.
     *
     * @return $this
     */
    public function assertDeleted()
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        PHPUnit::assertTrue(
            $this->job->isDeleted(),
            'Job was expected to be deleted, but was not.'
        );

        return $this;
    }

    /**
     * Assert that the job was not deleted from the queue.
     *
     * @return $this
     */
    public function assertNotDeleted()
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        PHPUnit::assertTrue(
            ! $this->job->isDeleted(),
            'Job was unexpectedly deleted.'
        );

        return $this;
    }

    /**
     * Assert that the job was manually failed.
     *
     * @return $this
     */
    public function assertFailed()
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        PHPUnit::assertTrue(
            $this->job->hasFailed(),
            'Job was expected to be manually failed, but was not.'
        );

        return $this;
    }

    /**
     * Assert that the job was manually failed with a specific exception.
     *
     * @param  \Throwable|string  $exception
     * @return $this
     */
    public function assertFailedWith($exception)
    {
        $this->assertFailed();

        if (is_string($exception) && class_exists($exception)) {
            PHPUnit::assertInstanceOf(
                $exception,
                $this->job->failedWith,
                'Expected job to be manually failed with ['.$exception.'] but job failed with ['.get_class($this->job->failedWith).'].'
            );

            return $this;
        }

        if (is_string($exception)) {
            $exception = new ManuallyFailedException($exception);
        }

        if ($exception instanceof Throwable) {
            PHPUnit::assertInstanceOf(
                get_class($exception),
                $this->job->failedWith,
                'Expected job to be manually failed with ['.get_class($exception).'] but job failed with ['.get_class($this->job->failedWith).'].'
            );

            PHPUnit::assertEquals(
                $exception->getCode(),
                $this->job->failedWith->getCode(),
                'Expected exception code ['.$exception->getCode().'] but job failed with exception code ['.$this->job->failedWith->getCode().'].'
            );

            PHPUnit::assertEquals(
                $exception->getMessage(),
                $this->job->failedWith->getMessage(),
                'Expected exception message ['.$exception->getMessage().'] but job failed with exception message ['.$this->job->failedWith->getMessage().'].');
        }

        return $this;
    }

    /**
     * Assert that the job was not manually failed.
     *
     * @return $this
     */
    public function assertNotFailed()
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        PHPUnit::assertTrue(
            ! $this->job->hasFailed(),
            'Job was unexpectedly failed manually.'
        );

        return $this;
    }

    /**
     * Assert that the job was released back onto the queue.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return $this
     */
    public function assertReleased($delay = null)
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        $delay = $delay instanceof DateTimeInterface
            ? $this->secondsUntil($delay)
            : $delay;

        PHPUnit::assertTrue(
            $this->job->isReleased(),
            'Job was expected to be released, but was not.'
        );

        if (! is_null($delay)) {
            PHPUnit::assertSame(
                $delay,
                $this->job->releaseDelay,
                "Expected job to be released with delay of [{$delay}] seconds, but was released with delay of [{$this->job->releaseDelay}] seconds."
            );
        }

        return $this;
    }

    /**
     * Assert that the job was not released back onto the queue.
     *
     * @return $this
     */
    public function assertNotReleased()
    {
        $this->ensureQueueInteractionsHaveBeenFaked();

        PHPUnit::assertTrue(
            ! $this->job->isReleased(),
            'Job was unexpectedly released.'
        );

        return $this;
    }

    /**
     * Ensure that queue interactions have been faked.
     *
     * @return void
     */
    private function ensureQueueInteractionsHaveBeenFaked()
    {
        if (! $this->job instanceof FakeJob) {
            throw new RuntimeException('Queue interactions have not been faked.');
        }
    }

    /**
     * Set the base queue job instance.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return $this
     */
    public function setJob(JobContract $job)
    {
        $this->job = $job;

        return $this;
    }
}
