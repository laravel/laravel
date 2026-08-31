<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Support\Facades\ParallelTesting;

trait TestCaches
{
    /**
     * The original cache prefix prior to appending the token.
     *
     * @var string|null
     */
    protected static $originalCachePrefix = null;

    /**
     * Boot test cache for parallel testing.
     *
     * @return void
     */
    protected function bootTestCache()
    {
        ParallelTesting::setUpTestCase(function () {
            if (ParallelTesting::option('without_cache')) {
                return;
            }

            $this->switchToCachePrefix($this->parallelSafeCachePrefix());
        });
    }

    /**
     * Get the test cache prefix.
     *
     * @return string
     */
    protected function parallelSafeCachePrefix()
    {
        self::$originalCachePrefix ??= $this->app['config']->get('cache.prefix', '');

        return self::$originalCachePrefix.'test_'.ParallelTesting::token().'_';
    }

    /**
     * Switch to the given cache prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    protected function switchToCachePrefix($prefix)
    {
        $this->app['config']->set('cache.prefix', $prefix);
    }
}
