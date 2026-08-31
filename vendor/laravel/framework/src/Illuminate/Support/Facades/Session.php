<?php

namespace Illuminate\Support\Facades;

/**
 * @method static bool shouldBlock()
 * @method static string|null blockDriver()
 * @method static int defaultRouteBlockLockSeconds()
 * @method static int defaultRouteBlockWaitSeconds()
 * @method static array getSessionConfig()
 * @method static string|null getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static mixed driver(string|null $driver = null)
 * @method static \Illuminate\Session\SessionManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \Illuminate\Session\SessionManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Illuminate\Session\SessionManager forgetDrivers()
 * @method static bool start()
 * @method static void save()
 * @method static void ageFlashData()
 * @method static array all()
 * @method static array only(array $keys)
 * @method static array except(array $keys)
 * @method static bool exists(\UnitEnum|string|array $key)
 * @method static bool missing(\UnitEnum|string|array $key)
 * @method static bool has(\UnitEnum|string|array $key)
 * @method static bool hasAny(\UnitEnum|string|array $key)
 * @method static mixed get(\UnitEnum|string $key, mixed $default = null)
 * @method static mixed pull(\UnitEnum|string $key, mixed $default = null)
 * @method static bool hasOldInput(string|null $key = null)
 * @method static mixed getOldInput(string|null $key = null, mixed $default = null)
 * @method static void replace(array $attributes)
 * @method static void put(\UnitEnum|string|array $key, mixed $value = null)
 * @method static mixed remember(\UnitEnum|string $key, \Closure $callback)
 * @method static void push(\UnitEnum|string $key, mixed $value)
 * @method static mixed increment(\UnitEnum|string $key, int $amount = 1)
 * @method static int decrement(\UnitEnum|string $key, int $amount = 1)
 * @method static void flash(\UnitEnum|string $key, mixed $value = true)
 * @method static void now(\UnitEnum|string $key, mixed $value)
 * @method static void reflash()
 * @method static void keep(mixed $keys = null)
 * @method static void flashInput(array $value)
 * @method static \Illuminate\Contracts\Cache\Repository cache()
 * @method static mixed remove(\UnitEnum|string $key)
 * @method static void forget(\UnitEnum|string|array $keys)
 * @method static void flush()
 * @method static bool invalidate()
 * @method static bool regenerate(bool $destroy = false)
 * @method static bool migrate(bool $destroy = false)
 * @method static bool isStarted()
 * @method static string getName()
 * @method static void setName(string $name)
 * @method static string id()
 * @method static string getId()
 * @method static void setId(string|null $id)
 * @method static bool isValidId(string|null $id)
 * @method static void setExists(bool $value)
 * @method static string token()
 * @method static void regenerateToken()
 * @method static bool hasPreviousUri()
 * @method static \Illuminate\Support\Uri previousUri()
 * @method static string|null previousUrl()
 * @method static void setPreviousUrl(string $url)
 * @method static string|null previousRoute()
 * @method static void setPreviousRoute(string|null $route)
 * @method static void passwordConfirmed()
 * @method static \SessionHandlerInterface getHandler()
 * @method static \SessionHandlerInterface setHandler(\SessionHandlerInterface $handler)
 * @method static bool handlerNeedsRequest()
 * @method static void setRequestOnHandler(\Illuminate\Http\Request $request)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Illuminate\Session\SessionManager
 */
class Session extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}
