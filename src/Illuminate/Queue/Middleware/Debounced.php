<?php

namespace Illuminate\Queue\Middleware;

use Illuminate\Bus\DebounceLock;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Repository as Cache;

class Debounced
{
    /**
     * The debounce key.
     */
    public string $key;

    /**
     * The debounce window in seconds.
     */
    public int $debounceFor;

    /**
     * The lock owner token acquired at dispatch time.
     */
    public string $owner;

    /**
     * Create a new middleware instance.
     *
     * The constructor runs at dispatch time and acquires the debounce lock.
     */
    public function __construct(string $key = '', int $debounceFor = 60)
    {
        $this->key = $key;
        $this->debounceFor = $debounceFor;

        $debounceLock = new DebounceLock(
            Container::getInstance()->make(Cache::class)
        );

        $this->owner = $debounceLock->acquire($this->createProxyJob());
    }

    /**
     * Handle the job.
     *
     * Runs at execution time. Checks if this dispatch is still the current
     * owner. If not, the job has been superseded and is deleted.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        $debounceLock = new DebounceLock(
            Container::getInstance()->make(Cache::class)
        );

        $proxyJob = $this->createProxyJob();

        if (! $debounceLock->isCurrentOwner($proxyJob, $this->owner)) {
            $job->delete();

            return;
        }

        try {
            return $next($job);
        } finally {
            $debounceLock->release($proxyJob);
        }
    }

    /**
     * Create a proxy job object that provides the interface DebounceLock expects.
     */
    protected function createProxyJob(): object
    {
        $key = $this->key;
        $debounceFor = $this->debounceFor;

        return new class($key, $debounceFor)
        {
            public function __construct(
                private string $key,
                private int $debounceFor,
            ) {}

            public function debounceId(): string
            {
                return $this->key;
            }

            public function debounceFor(): int
            {
                return $this->debounceFor;
            }

            public function displayName(): string
            {
                return $this->key;
            }
        };
    }
}
