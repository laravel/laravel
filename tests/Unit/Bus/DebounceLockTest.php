<?php

namespace Tests\Unit\Bus;

use Illuminate\Bus\DebounceLock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldBeDebounced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\Attributes\DebounceFor;
use Illuminate\Queue\InteractsWithQueue;
use Mockery;
use PHPUnit\Framework\TestCase;

class DebounceLockTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_acquire_returns_non_empty_owner_token(): void
    {
        $lock = $this->createMockLock('test-owner-token-123');
        $cache = $this->createMockCacheForAcquire($lock, 60);

        $debounceLock = new DebounceLock($cache);
        $job = new DebounceLockTestJob;

        $owner = $debounceLock->acquire($job);

        $this->assertNotEmpty($owner);
        $this->assertSame('test-owner-token-123', $owner);
    }

    public function test_acquire_force_releases_prior_lock_and_acquires_new(): void
    {
        $lock = Mockery::mock(Lock::class);
        $lock->shouldReceive('forceRelease')->once()->ordered();
        $lock->shouldReceive('get')->once()->ordered();
        $lock->shouldReceive('owner')->once()->andReturn('owner-1');

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('lock')
            ->with(DebounceLock::getKey(new DebounceLockTestJob), 60)
            ->once()
            ->andReturn($lock);

        $debounceLock = new DebounceLock($cache);
        $owner1 = $debounceLock->acquire(new DebounceLockTestJob);

        $lock2 = Mockery::mock(Lock::class);
        $lock2->shouldReceive('forceRelease')->once()->ordered();
        $lock2->shouldReceive('get')->once()->ordered();
        $lock2->shouldReceive('owner')->once()->andReturn('owner-2');

        $cache2 = Mockery::mock(Cache::class);
        $cache2->shouldReceive('lock')
            ->with(DebounceLock::getKey(new DebounceLockTestJob), 60)
            ->once()
            ->andReturn($lock2);

        $debounceLock2 = new DebounceLock($cache2);
        $owner2 = $debounceLock2->acquire(new DebounceLockTestJob);

        $this->assertNotSame($owner1, $owner2);
    }

    public function test_is_current_owner_returns_true_for_current_owner(): void
    {
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturn(true);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')
            ->with(DebounceLock::getKey(new DebounceLockTestJob), 'owner-token')
            ->once()
            ->andReturn($restoredLock);

        $debounceLock = new DebounceLock($cache);
        $result = $debounceLock->isCurrentOwner(new DebounceLockTestJob, 'owner-token');

        $this->assertTrue($result);
    }

    public function test_is_current_owner_returns_false_after_another_acquire(): void
    {
        $restoredLock = Mockery::mock(Lock::class);
        $restoredLock->shouldReceive('isOwnedByCurrentProcess')->once()->andReturn(false);

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('restoreLock')
            ->with(DebounceLock::getKey(new DebounceLockTestJob), 'old-owner')
            ->once()
            ->andReturn($restoredLock);

        $debounceLock = new DebounceLock($cache);
        $result = $debounceLock->isCurrentOwner(new DebounceLockTestJob, 'old-owner');

        $this->assertFalse($result);
    }

    public function test_release_removes_the_lock(): void
    {
        $lock = Mockery::mock(Lock::class);
        $lock->shouldReceive('forceRelease')->once();

        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('lock')
            ->with(DebounceLock::getKey(new DebounceLockTestJob))
            ->once()
            ->andReturn($lock);

        $debounceLock = new DebounceLock($cache);
        $debounceLock->release(new DebounceLockTestJob);

        // Mockery verifies forceRelease was called; explicit assertion for PHPUnit
        $this->assertTrue(true);
    }

    public function test_get_key_returns_correct_format(): void
    {
        $job = new DebounceLockTestJob;
        $key = DebounceLock::getKey($job);

        $expectedHash = hash('xxh128', $job->displayName());
        $this->assertSame('laravel_debounced_job:'.$expectedHash.':test-entity-1', $key);
    }

    public function test_get_key_uses_empty_string_when_no_debounce_id(): void
    {
        $job = new DebounceLockTestJobNoId;
        $key = DebounceLock::getKey($job);

        $expectedHash = hash('xxh128', $job->displayName());
        $this->assertSame('laravel_debounced_job:'.$expectedHash.':', $key);
    }

    public function test_acquire_uses_debounce_via_custom_cache_store(): void
    {
        $customLock = $this->createMockLock('custom-owner');
        $customCache = Mockery::mock(Cache::class);
        $customCache->shouldReceive('lock')
            ->once()
            ->andReturn($customLock);

        $defaultCache = Mockery::mock(Cache::class);
        $defaultCache->shouldNotReceive('lock');

        $job = new DebounceLockTestJobWithVia($customCache);

        $debounceLock = new DebounceLock($defaultCache);
        $owner = $debounceLock->acquire($job);

        $this->assertSame('custom-owner', $owner);
    }

    public function test_acquire_reads_debounce_for_from_attribute(): void
    {
        $lock = $this->createMockLock('attr-owner');
        $cache = Mockery::mock(Cache::class);

        // DebounceFor(45) -> TTL = max(45 * 2, 10) = 90
        $cache->shouldReceive('lock')
            ->with(Mockery::any(), 90)
            ->once()
            ->andReturn($lock);

        $debounceLock = new DebounceLock($cache);
        $job = new DebounceLockTestJobWithAttribute;

        $owner = $debounceLock->acquire($job);
        $this->assertSame('attr-owner', $owner);
    }

    public function test_acquire_prefers_method_over_attribute(): void
    {
        $lock = $this->createMockLock('method-owner');
        $cache = Mockery::mock(Cache::class);

        // debounceFor() method returns 30, so TTL = max(30 * 2, 10) = 60
        // The attribute says 45, but the method should win
        $cache->shouldReceive('lock')
            ->with(Mockery::any(), 60)
            ->once()
            ->andReturn($lock);

        $debounceLock = new DebounceLock($cache);
        $job = new DebounceLockTestJobMethodAndAttribute;

        $owner = $debounceLock->acquire($job);
        $this->assertSame('method-owner', $owner);
    }

    /**
     * Create a mock lock with standard behavior.
     */
    private function createMockLock(string $ownerToken): Lock
    {
        $lock = Mockery::mock(Lock::class);
        $lock->shouldReceive('forceRelease')->once();
        $lock->shouldReceive('get')->once();
        $lock->shouldReceive('owner')->once()->andReturn($ownerToken);

        return $lock;
    }

    /**
     * Create a mock cache that returns the given lock for acquire().
     */
    private function createMockCacheForAcquire(Lock $lock, int $expectedTtl): Cache
    {
        $cache = Mockery::mock(Cache::class);
        $cache->shouldReceive('lock')
            ->with(Mockery::any(), $expectedTtl)
            ->once()
            ->andReturn($lock);

        return $cache;
    }
}

class DebounceLockTestJob implements ShouldQueue, ShouldBeDebounced
{
    use Queueable, InteractsWithQueue;

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
        return 'DebounceLockTestJob';
    }

    public function handle(): void
    {
    }
}

class DebounceLockTestJobNoId implements ShouldQueue, ShouldBeDebounced
{
    use Queueable, InteractsWithQueue;

    public function displayName(): string
    {
        return 'DebounceLockTestJobNoId';
    }

    public function handle(): void
    {
    }
}

class DebounceLockTestJobWithVia implements ShouldQueue, ShouldBeDebounced
{
    use Queueable, InteractsWithQueue;

    private Cache $customCache;

    public function __construct(Cache $customCache)
    {
        $this->customCache = $customCache;
    }

    public function debounceId(): string
    {
        return 'via-test';
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function displayName(): string
    {
        return 'DebounceLockTestJobWithVia';
    }

    public function debounceVia(): Cache
    {
        return $this->customCache;
    }

    public function handle(): void
    {
    }
}

#[DebounceFor(45)]
class DebounceLockTestJobWithAttribute implements ShouldQueue, ShouldBeDebounced
{
    use Queueable, InteractsWithQueue;

    public function debounceId(): string
    {
        return 'attr-test';
    }

    public function displayName(): string
    {
        return 'DebounceLockTestJobWithAttribute';
    }

    public function handle(): void
    {
    }
}

#[DebounceFor(45)]
class DebounceLockTestJobMethodAndAttribute implements ShouldQueue, ShouldBeDebounced
{
    use Queueable, InteractsWithQueue;

    public function debounceId(): string
    {
        return 'both-test';
    }

    public function debounceFor(): int
    {
        return 30;
    }

    public function displayName(): string
    {
        return 'DebounceLockTestJobMethodAndAttribute';
    }

    public function handle(): void
    {
    }
}
