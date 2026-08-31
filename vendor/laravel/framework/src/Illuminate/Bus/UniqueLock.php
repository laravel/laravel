<?php

namespace Illuminate\Bus;

use Illuminate\Contracts\Cache\Repository as Cache;

class UniqueLock
{
    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new unique lock manager instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Attempt to acquire a lock for the given job.
     *
     * @param  mixed  $job
     * @return bool
     */
    public function acquire($job)
    {
        $uniqueFor = method_exists($job, 'uniqueFor')
            ? $job->uniqueFor()
            : ($job->uniqueFor ?? 0);

        $cache = method_exists($job, 'uniqueVia')
            ? ($job->uniqueVia() ?? $this->cache)
            : $this->cache;

        return (bool) $cache->lock($this->getKey($job), $uniqueFor)->get();
    }

    /**
     * Release the lock for the given job.
     *
     * @param  mixed  $job
     * @return void
     */
    public function release($job)
    {
        $cache = method_exists($job, 'uniqueVia')
            ? ($job->uniqueVia() ?? $this->cache)
            : $this->cache;

        $cache->lock($this->getKey($job))->forceRelease();
    }

    /**
     * Generate the lock key for the given job.
     *
     * @param  mixed  $job
     * @return string
     */
    public static function getKey($job)
    {
        $uniqueId = method_exists($job, 'uniqueId')
            ? $job->uniqueId()
            : ($job->uniqueId ?? '');

        $jobName = method_exists($job, 'displayName')
            ? hash('xxh128', $job->displayName())
            : get_class($job);

        return 'laravel_unique_job:'.$jobName.':'.$uniqueId;
    }
}
