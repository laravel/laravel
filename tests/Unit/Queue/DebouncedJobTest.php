<?php

namespace Tests\Unit\Queue;

use Illuminate\Bus\DebounceLock;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Contracts\Queue\ShouldBeDebounced;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Bus\InteractsWithDebouncedJobs;
use Illuminate\Queue\ChecksDebouncedJobs;
use Illuminate\Queue\Events\JobDebounced;
use Illuminate\Queue\InteractsWithQueue;
use LogicException;
use Mockery;
use PHPUnit\Framework\TestCase;

class DebouncedJobTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance(null);
    }

    public function test_dispatching_debounced_job_sets_debounce_owner(): void
    {
        $cache = $this->setupContainerWithMockCache('dispatch-owner');
        $job = new DebouncedTestJob;

        $harness = new PendingDispatchHarness($job);
        $harness->callAcquireDebounceLock();

        $this->assertSame('dispatch-owner', $job->debounceOwner);
    }

    public function test_dispatching_debounced_job_sets_delay(): void
    {
        $this->setupContainerWithMockCache('owner');
        $job = new DebouncedTestJob;

        $harness = new PendingDispatchHarness($job);
        $harness->callAcquireDebounceLock();

        $this->assertSame(30, $job->delay);
    }

    public function test_second_dispatch_replaces_first_lock_owner(): void
    {
        $cache = $this->setupContainerWithMockCache('owner-1');
        $job1 = new DebouncedTestJob;
        $harness1 = new PendingDispatchHarness($job1);
        $harness1->callAcquireDebounceLock();

        // Replace cache mock with new owner
        $this->setupContainerWithMockCache('owner-2');
        $job2 = new DebouncedTestJob;
        $harness2 = new PendingDispatchHarness($job2);
        $harness2->callAcquireDebounceLock();

        $this->assertSame('owner-1', $job1->debounceOwner);
        $this->assertSame('owner-2', $job2->debounceOwner);
        $this->assertNotSame($job1->debounceOwner, $job2->debounceOwner);
    }

    public function test_superseded_job_is_detected_via_ownership_check(): void
    {
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturn(false);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')->once()->andReturn($restoredLock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);

        $job = new DebouncedTestJob;
        $job->debounceOwner = 'old-owner';

        $harness = new CallQueuedHandlerHarness($container);
        $this->assertTrue($harness->callCommandWasDebounced($job));
    }

    public function test_current_owner_job_passes_ownership_check(): void
    {
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturn(true);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')->once()->andReturn($restoredLock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);

        $job = new DebouncedTestJob;
        $job->debounceOwner = 'current-owner';

        $harness = new CallQueuedHandlerHarness($container);
        $this->assertFalse($harness->callCommandWasDebounced($job));
    }

    public function test_debounce_owner_survives_serialization(): void
    {
        $job = new DebouncedTestJob;
        $job->debounceOwner = 'test-owner-token-xyz';

        $restored = unserialize(serialize($job));

        $this->assertSame('test-owner-token-xyz', $restored->debounceOwner);
    }

    public function test_different_debounce_ids_do_not_interfere(): void
    {
        $job1 = new DebouncedTestJobWithId('entity-1');
        $job2 = new DebouncedTestJobWithId('entity-2');

        $key1 = DebounceLock::getKey($job1);
        $key2 = DebounceLock::getKey($job2);

        $this->assertNotSame($key1, $key2);
        $this->assertStringContainsString('entity-1', $key1);
        $this->assertStringContainsString('entity-2', $key2);
    }

    public function test_should_be_debounced_and_should_be_unique_throws_logic_exception(): void
    {
        $this->setupContainerWithMockCache('owner');

        $job = new DebouncedAndUniqueTestJob;
        $harness = new PendingDispatchHarness($job);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('ShouldBeDebounced and ShouldBeUnique');

        $harness->callAcquireDebounceLock();
    }

    public function test_job_debounced_event_fires_when_superseded(): void
    {
        $events = Mockery::mock('events');
        $events->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(JobDebounced::class))
            ->andReturnUsing(function (JobDebounced $event) {
                $this->assertSame('redis', $event->connectionName);
                $this->assertInstanceOf(DebouncedTestJob::class, $event->command);
            });

        $container = new Container;
        $container->instance('events', $events);
        Container::setInstance($container);

        $queueJob = Mockery::mock(QueueJob::class);
        $queueJob->shouldReceive('getConnectionName')->once()->andReturn('redis');
        $queueJob->shouldReceive('delete')->once();

        $command = new DebouncedTestJob;

        $harness = new CallQueuedHandlerHarness($container);
        $harness->callHandleDebouncedJob($queueJob, $command);
    }

    public function test_lock_is_released_after_successful_execution(): void
    {
        $lock = Mockery::mock(Lock::class);
        $lock->shouldReceive('forceRelease')->once();

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('lock')
            ->with(DebounceLock::getKey(new DebouncedTestJob))
            ->once()
            ->andReturn($lock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);

        $job = new DebouncedTestJob;

        $harness = new CallQueuedHandlerHarness($container);
        $harness->callEnsureDebounceLockIsReleased($job);

        $this->assertTrue(true); // Mockery verifies forceRelease was called
    }

    public function test_job_with_no_debounce_owner_is_not_treated_as_debounced(): void
    {
        $container = new Container;
        Container::setInstance($container);

        $job = new DebouncedTestJob;
        // debounceOwner not set

        $harness = new CallQueuedHandlerHarness($container);
        $this->assertFalse($harness->callCommandWasDebounced($job));
    }

    /**
     * Set up a container with a mock cache that returns a lock for acquire().
     */
    private function setupContainerWithMockCache(string $ownerToken): Cache
    {
        $lock = Mockery::mock(Lock::class);
        $lock->shouldReceive('forceRelease');
        $lock->shouldReceive('get');
        $lock->shouldReceive('owner')->andReturn($ownerToken);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('lock')->andReturn($lock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);

        return $cache;
    }
}

/**
 * Test harness that exposes the InteractsWithDebouncedJobs trait methods.
 */
class PendingDispatchHarness
{
    use InteractsWithDebouncedJobs;

    protected $job;

    public function __construct($job)
    {
        $this->job = $job;
    }

    public function callAcquireDebounceLock(): void
    {
        $this->acquireDebounceLock();
    }
}

/**
 * Test harness that exposes the ChecksDebouncedJobs trait methods.
 */
class CallQueuedHandlerHarness
{
    use ChecksDebouncedJobs;

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function callCommandWasDebounced($command): bool
    {
        return $this->commandWasDebounced($command);
    }

    public function callHandleDebouncedJob($job, $command): void
    {
        $this->handleDebouncedJob($job, $command);
    }

    public function callEnsureDebounceLockIsReleased($command): void
    {
        $this->ensureDebounceLockIsReleased($command);
    }
}

class DebouncedTestJob implements ShouldBeDebounced, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public string $debounceOwner = '';

    public static bool $handled = false;

    public function debounceId(): string
    {
        return 'test-entity-1';
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function displayName(): string
    {
        return 'DebouncedTestJob';
    }

    public function handle(): void
    {
        static::$handled = true;
    }
}

class DebouncedTestJobWithId implements ShouldBeDebounced, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public string $debounceOwner = '';

    private string $entityId;

    public function __construct(string $entityId = '')
    {
        $this->entityId = $entityId;
    }

    public function debounceId(): string
    {
        return $this->entityId;
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function displayName(): string
    {
        return 'DebouncedTestJobWithId';
    }

    public function handle(): void {}
}

class DebouncedAndUniqueTestJob implements ShouldBeDebounced, ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public string $debounceOwner = '';

    public function debounceId(): string
    {
        return 'test';
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function handle(): void {}
}
