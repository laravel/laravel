<?php

namespace Illuminate\Concurrency;

use Illuminate\Process\Factory as ProcessFactory;
use Illuminate\Support\MultipleInstanceManager;
use RuntimeException;
use Spatie\Fork\Fork;

/**
 * @mixin \Illuminate\Contracts\Concurrency\Driver
 */
class ConcurrencyManager extends MultipleInstanceManager
{
    /**
     * Get a driver instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function driver($name = null)
    {
        return $this->instance($name);
    }

    /**
     * Create an instance of the process concurrency driver.
     *
     * @return \Illuminate\Concurrency\ProcessDriver
     */
    public function createProcessDriver()
    {
        return new ProcessDriver($this->app->make(ProcessFactory::class));
    }

    /**
     * Create an instance of the fork concurrency driver.
     *
     * @return \Illuminate\Concurrency\ForkDriver
     *
     * @throws \RuntimeException
     */
    public function createForkDriver()
    {
        if (! $this->app->runningInConsole()) {
            throw new RuntimeException('Due to PHP limitations, the fork driver may not be used within web requests.');
        }

        if (! class_exists(Fork::class)) {
            throw new RuntimeException('Please install the "spatie/fork" Composer package in order to utilize the "fork" driver.');
        }

        return new ForkDriver;
    }

    /**
     * Create an instance of the sync concurrency driver.
     *
     * @return \Illuminate\Concurrency\SyncDriver
     */
    public function createSyncDriver()
    {
        return new SyncDriver;
    }

    /**
     * Get the default instance name.
     *
     * @return string
     */
    public function getDefaultInstance()
    {
        return $this->app['config']['concurrency.default']
            ?? $this->app['config']['concurrency.driver']
            ?? 'process';
    }

    /**
     * Set the default instance name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultInstance($name)
    {
        $this->app['config']['concurrency.default'] = $name;
        $this->app['config']['concurrency.driver'] = $name;
    }

    /**
     * Get the instance specific configuration.
     *
     * @param  string  $name
     * @return array
     */
    public function getInstanceConfig($name)
    {
        return $this->app['config']->get(
            'concurrency.driver.'.$name, ['driver' => $name],
        );
    }
}
