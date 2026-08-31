<?php

namespace Illuminate\Support\Facades;

use Illuminate\Foundation\MaintenanceModeManager;

/**
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \Illuminate\Foundation\MaintenanceModeManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \Illuminate\Foundation\MaintenanceModeManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Illuminate\Foundation\MaintenanceModeManager forgetDrivers()
 *
 * @see \Illuminate\Foundation\MaintenanceModeManager
 */
class MaintenanceMode extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MaintenanceModeManager::class;
    }
}
