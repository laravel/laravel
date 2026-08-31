<?php

namespace Illuminate\Foundation;

use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Foundation\MaintenanceMode;

class CacheBasedMaintenanceMode implements MaintenanceMode
{
    /**
     * The cache factory.
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * The cache store that should be utilized.
     *
     * @var string
     */
    protected $store;

    /**
     * The cache key to use when storing maintenance mode information.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new cache based maintenance mode implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Factory  $cache
     * @param  string  $store
     * @param  string  $key
     */
    public function __construct(Factory $cache, string $store, string $key)
    {
        $this->cache = $cache;
        $this->store = $store;
        $this->key = $key;
    }

    /**
     * Take the application down for maintenance.
     *
     * @param  array  $payload
     * @return void
     */
    public function activate(array $payload): void
    {
        $this->getStore()->put($this->key, $payload);
    }

    /**
     * Take the application out of maintenance.
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->getStore()->forget($this->key);
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @return bool
     */
    public function active(): bool
    {
        return $this->getStore()->has($this->key);
    }

    /**
     * Get the data array which was provided when the application was placed into maintenance.
     *
     * @return array
     */
    public function data(): array
    {
        return $this->getStore()->get($this->key);
    }

    /**
     * Get the cache store to use.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function getStore(): Repository
    {
        return $this->cache->store($this->store);
    }
}
