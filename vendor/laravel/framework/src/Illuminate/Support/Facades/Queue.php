<?php

namespace Illuminate\Support\Facades;

use Illuminate\Queue\Worker;
use Illuminate\Support\Testing\Fakes\QueueFake;

/**
 * @method static void before(mixed $callback)
 * @method static void after(mixed $callback)
 * @method static void exceptionOccurred(mixed $callback)
 * @method static void looping(mixed $callback)
 * @method static void failing(mixed $callback)
 * @method static void starting(mixed $callback)
 * @method static void stopping(mixed $callback)
 * @method static bool connected(string|null $name = null)
 * @method static \Illuminate\Contracts\Queue\Queue connection(string|null $name = null)
 * @method static void pause(string $connection, string $queue)
 * @method static void pauseFor(string $connection, string $queue, \DateTimeInterface|\DateInterval|int $ttl)
 * @method static void resume(string $connection, string $queue)
 * @method static bool isPaused(string $connection, string $queue)
 * @method static void withoutInterruptionPolling()
 * @method static void extend(string $driver, \Closure $resolver)
 * @method static void addConnector(string $driver, \Closure $resolver)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static string getName(string|null $connection = null)
 * @method static \Illuminate\Contracts\Foundation\Application getApplication()
 * @method static \Illuminate\Queue\QueueManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static int size(string|null $queue = null)
 * @method static mixed push(string|object $job, mixed $data = '', string|null $queue = null)
 * @method static mixed pushOn(string $queue, string|object $job, mixed $data = '')
 * @method static mixed pushRaw(string $payload, string|null $queue = null, array $options = [])
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '', string|null $queue = null)
 * @method static mixed laterOn(string $queue, \DateTimeInterface|\DateInterval|int $delay, string|object $job, mixed $data = '')
 * @method static mixed bulk(array $jobs, mixed $data = '', string|null $queue = null)
 * @method static \Illuminate\Contracts\Queue\Job|null pop(string|null $queue = null)
 * @method static string getConnectionName()
 * @method static \Illuminate\Contracts\Queue\Queue setConnectionName(string $name)
 * @method static int pendingSize(string|null $queue = null)
 * @method static int delayedSize(string|null $queue = null)
 * @method static int reservedSize(string|null $queue = null)
 * @method static int|null creationTimeOfOldestPendingJob(string|null $queue = null)
 * @method static mixed getJobTries(mixed $job)
 * @method static mixed getJobBackoff(mixed $job)
 * @method static mixed getJobExpiration(mixed $job)
 * @method static void createPayloadUsing(callable|null $callback)
 * @method static array getConfig()
 * @method static \Illuminate\Queue\Queue setConfig(array $config)
 * @method static \Illuminate\Container\Container getContainer()
 * @method static void setContainer(\Illuminate\Container\Container $container)
 * @method static \Illuminate\Support\Testing\Fakes\QueueFake except(array|string $jobsToBeQueued)
 * @method static void assertPushed(string|\Closure $job, callable|int|null $callback = null)
 * @method static void assertPushedTimes(string $job, int $times = 1)
 * @method static void assertPushedOn(string $queue, string|\Closure $job, callable|null $callback = null)
 * @method static void assertPushedWithChain(string $job, array $expectedChain = [], callable|null $callback = null)
 * @method static void assertPushedWithoutChain(string $job, callable|null $callback = null)
 * @method static void assertClosurePushed(callable|int|null $callback = null)
 * @method static void assertClosureNotPushed(callable|null $callback = null)
 * @method static void assertNotPushed(string|\Closure $job, callable|null $callback = null)
 * @method static void assertCount(int $expectedCount)
 * @method static void assertNothingPushed()
 * @method static \Illuminate\Support\Collection pushed(string $job, callable|null $callback = null)
 * @method static \Illuminate\Support\Collection pushedRaw(null|\Closure $callback = null)
 * @method static \Illuminate\Support\Collection listenersPushed(string $listenerClass, \Closure|null $callback = null)
 * @method static bool hasPushed(string $job)
 * @method static bool shouldFakeJob(object $job)
 * @method static array pushedJobs()
 * @method static array rawPushes()
 * @method static \Illuminate\Support\Testing\Fakes\QueueFake serializeAndRestore(bool $serializeAndRestore = true)
 * @method static void releaseUniqueJobLocks()
 *
 * @see \Illuminate\Queue\QueueManager
 * @see \Illuminate\Queue\Queue
 * @see \Illuminate\Support\Testing\Fakes\QueueFake
 */
class Queue extends Facade
{
    /**
     * Register a callback to be executed to pick jobs.
     *
     * @param  string  $workerName
     * @param  callable  $callback
     * @return void
     */
    public static function popUsing($workerName, $callback)
    {
        Worker::popUsing($workerName, $callback);
    }

    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $jobsToFake
     * @return \Illuminate\Support\Testing\Fakes\QueueFake
     */
    public static function fake($jobsToFake = [])
    {
        $actualQueueManager = static::isFake()
            ? tap(static::getFacadeRoot(), fn ($fake) => $fake->releaseUniqueJobLocks())->queue
            : static::getFacadeRoot();

        return tap(new QueueFake(static::getFacadeApplication(), $jobsToFake, $actualQueueManager), function ($fake) {
            static::swap($fake);
        });
    }

    /**
     * Replace the bound instance with a fake that fakes all jobs except the given jobs.
     *
     * @param  string[]|string  $jobsToAllow
     * @return \Illuminate\Support\Testing\Fakes\QueueFake
     */
    public static function fakeExcept($jobsToAllow)
    {
        return static::fake()->except($jobsToAllow);
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $jobsToFake
     * @return mixed
     */
    public static function fakeFor(callable $callable, array $jobsToFake = [])
    {
        $originalQueueManager = static::getFacadeRoot();

        static::fake($jobsToFake);

        try {
            return $callable();
        } finally {
            static::swap($originalQueueManager);
        }
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
     *
     * @param  callable  $callable
     * @param  array  $jobsToAllow
     * @return mixed
     */
    public static function fakeExceptFor(callable $callable, array $jobsToAllow = [])
    {
        $originalQueueManager = static::getFacadeRoot();

        static::fakeExcept($jobsToAllow);

        try {
            return $callable();
        } finally {
            static::swap($originalQueueManager);
        }
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queue';
    }
}
