<?php

namespace Illuminate\Support\Facades;

use Illuminate\Concurrency\ConcurrencyManager;

/**
 * @method static mixed driver(string|null $name = null)
 * @method static \Illuminate\Concurrency\ProcessDriver createProcessDriver()
 * @method static \Illuminate\Concurrency\ForkDriver createForkDriver()
 * @method static \Illuminate\Concurrency\SyncDriver createSyncDriver()
 * @method static string getDefaultInstance()
 * @method static void setDefaultInstance(string $name)
 * @method static array getInstanceConfig(string $name)
 * @method static mixed instance(string|null $name = null)
 * @method static \Illuminate\Concurrency\ConcurrencyManager forgetInstance(array|string|null $name = null)
 * @method static void purge(string|null $name = null)
 * @method static \Illuminate\Concurrency\ConcurrencyManager extend(string $name, \Closure $callback)
 * @method static \Illuminate\Concurrency\ConcurrencyManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static array run(\Closure|array $tasks)
 * @method static \Illuminate\Support\Defer\DeferredCallback defer(\Closure|array $tasks)
 *
 * @see \Illuminate\Concurrency\ConcurrencyManager
 */
class Concurrency extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConcurrencyManager::class;
    }
}
