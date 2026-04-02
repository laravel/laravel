<?php

namespace Illuminate\Bus;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Queue\Attributes\DebounceFor;
use Illuminate\Queue\Attributes\ReadsQueueAttributes;

class DebounceLock
{
    use ReadsQueueAttributes;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new debounce lock manager instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Acquire a debounce lock for the given job.
     *
     * Force-releases any existing lock and acquires a new one,
     * implementing last-writer-wins semantics.
     *
     * @param  mixed  $job
     * @return string
     */
    public function acquire($job): string
    {
        $debounceFor = method_exists($job, 'debounceFor')
            ? $job->debounceFor()
            : ($this->getAttributeValue($job, DebounceFor::class, 'debounceFor') ?? 0);

        $ttl = max($debounceFor * 2, 10);

        $cache = $this->resolveCache($job);

        $lock = $cache->lock(static::getKey($job), $ttl);

        $lock->forceRelease();
        $lock->get();

        return $lock->owner();
    }

    /**
     * Determine if the given owner is the current lock owner.
     *
     * @param  mixed  $job
     * @param  string  $owner
     * @return bool
     */
    public function isCurrentOwner($job, string $owner): bool
    {
        $cache = $this->resolveCache($job);

        return $cache->restoreLock(static::getKey($job), $owner)
            ->isOwnedByCurrentProcess();
    }

    /**
     * Release the debounce lock for the given job.
     *
     * @param  mixed  $job
     * @return void
     */
    public function release($job): void
    {
        $cache = $this->resolveCache($job);

        $cache->lock(static::getKey($job))->forceRelease();
    }

    /**
     * Generate the lock key for the given job.
     *
     * @param  mixed  $job
     * @return string
     */
    public static function getKey($job): string
    {
        $debounceId = method_exists($job, 'debounceId')
            ? $job->debounceId()
            : ($job->debounceId ?? '');

        $jobName = method_exists($job, 'displayName')
            ? hash('xxh128', $job->displayName())
            : get_class($job);

        return 'laravel_debounced_job:'.$jobName.':'.$debounceId;
    }

    /**
     * Resolve the cache store for the given job.
     *
     * @param  mixed  $job
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function resolveCache($job): Cache
    {
        return method_exists($job, 'debounceVia')
            ? ($job->debounceVia() ?? $this->cache)
            : $this->cache;
    }
}
