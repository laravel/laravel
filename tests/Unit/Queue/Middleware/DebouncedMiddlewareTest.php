<?php

namespace Tests\Unit\Queue\Middleware;

use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Queue\Middleware\Debounced;
use Mockery;
use PHPUnit\Framework\TestCase;

class DebouncedMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        Container::setInstance(null);
    }

    public function test_constructor_acquires_lock_via_debounce_lock(): void
    {
        $this->setupContainerWithMockCacheForAcquire('ctor-owner');

        $middleware = new Debounced('my-key', 60);

        $this->assertSame('ctor-owner', $middleware->owner);
    }

    public function test_handle_proceeds_when_current_owner(): void
    {
        $this->setupContainerWithMockCacheForAcquire('current-owner');
        $middleware = new Debounced('test-key', 60);

        // Now set up cache for handle() -- isCurrentOwner + release
        $this->setupContainerWithMockCacheForHandle(isOwner: true);

        $job = Mockery::mock(QueueJob::class);
        $job->shouldNotReceive('delete');

        $nextCalled = false;
        $middleware->handle($job, function ($passedJob) use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertTrue($nextCalled);
    }

    public function test_handle_deletes_job_when_not_current_owner(): void
    {
        $this->setupContainerWithMockCacheForAcquire('old-owner');
        $middleware = new Debounced('test-key', 60);

        // Now set up cache for handle() -- isCurrentOwner returns false
        $this->setupContainerWithMockCacheForHandle(isOwner: false);

        $job = Mockery::mock(QueueJob::class);
        $job->shouldReceive('delete')->once();

        $nextCalled = false;
        $middleware->handle($job, function ($passedJob) use (&$nextCalled) {
            $nextCalled = true;
        });

        $this->assertFalse($nextCalled);
    }

    public function test_handle_releases_lock_after_successful_execution(): void
    {
        $this->setupContainerWithMockCacheForAcquire('release-owner');
        $middleware = new Debounced('test-key', 60);

        // For handle: restore lock (isOwner), then release lock
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturn(true);

        $releaseLock = Mockery::mock(Lock::class);
        $releaseLock->shouldReceive('forceRelease')->once();

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')->once()->andReturn($restoredLock);
        $cache->shouldReceive('lock')->once()->andReturn($releaseLock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);

        $job = Mockery::mock(QueueJob::class);

        $middleware->handle($job, function ($passedJob) {
            // Do nothing -- successful execution
        });

        // Mockery verifies forceRelease was called
        $this->assertTrue(true);
    }

    public function test_middleware_properties_survive_serialization(): void
    {
        $this->setupContainerWithMockCacheForAcquire('serial-owner');

        $middleware = new Debounced('persist-key', 45);
        $restored = unserialize(serialize($middleware));

        $this->assertSame('persist-key', $restored->key);
        $this->assertSame(45, $restored->debounceFor);
        $this->assertSame('serial-owner', $restored->owner);
    }

    /**
     * Set up container with mock cache for acquire() (constructor).
     */
    private function setupContainerWithMockCacheForAcquire(string $ownerToken): void
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
    }

    /**
     * Set up container with mock cache for handle() checks.
     */
    private function setupContainerWithMockCacheForHandle(bool $isOwner): void
    {
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->andReturn($isOwner);

        $releaseLock = Mockery::mock(Lock::class);
        $releaseLock->shouldReceive('forceRelease');

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')->andReturn($restoredLock);
        $cache->shouldReceive('lock')->andReturn($releaseLock);

        $container = new Container;
        $container->instance(Cache::class, $cache);
        Container::setInstance($container);
    }
}
