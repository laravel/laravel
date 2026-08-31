<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;

/**
 * @method static bool has(\UnitEnum|array|string $ability)
 * @method static \Illuminate\Auth\Access\Response allowIf(\Illuminate\Auth\Access\Response|\Closure|bool $condition, string|null $message = null, string|null $code = null)
 * @method static \Illuminate\Auth\Access\Response denyIf(\Illuminate\Auth\Access\Response|\Closure|bool $condition, string|null $message = null, string|null $code = null)
 * @method static \Illuminate\Auth\Access\Gate define(\UnitEnum|string $ability, callable|array|string $callback)
 * @method static \Illuminate\Auth\Access\Gate resource(string $name, string $class, array|null $abilities = null)
 * @method static \Illuminate\Auth\Access\Gate policy(string $class, string $policy)
 * @method static \Illuminate\Auth\Access\Gate before(callable $callback)
 * @method static \Illuminate\Auth\Access\Gate after(callable $callback)
 * @method static bool allows(iterable|\UnitEnum|string $ability, mixed $arguments = [])
 * @method static bool denies(iterable|\UnitEnum|string $ability, mixed $arguments = [])
 * @method static bool check(iterable|\UnitEnum|string $abilities, mixed $arguments = [])
 * @method static bool any(iterable|\UnitEnum|string $abilities, mixed $arguments = [])
 * @method static bool none(iterable|\UnitEnum|string $abilities, mixed $arguments = [])
 * @method static \Illuminate\Auth\Access\Response authorize(\UnitEnum|string $ability, mixed $arguments = [])
 * @method static \Illuminate\Auth\Access\Response inspect(\UnitEnum|string $ability, mixed $arguments = [])
 * @method static mixed raw(string $ability, mixed $arguments = [])
 * @method static mixed getPolicyFor(object|string $class)
 * @method static \Illuminate\Auth\Access\Gate guessPolicyNamesUsing(callable $callback)
 * @method static mixed resolvePolicy(object|string $class)
 * @method static \Illuminate\Auth\Access\Gate forUser(\Illuminate\Contracts\Auth\Authenticatable|mixed $user)
 * @method static array abilities()
 * @method static array policies()
 * @method static \Illuminate\Auth\Access\Gate defaultDenialResponse(\Illuminate\Auth\Access\Response $response)
 * @method static \Illuminate\Auth\Access\Gate setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Illuminate\Auth\Access\Response denyWithStatus(int $status, string|null $message = null, int|null $code = null)
 * @method static \Illuminate\Auth\Access\Response denyAsNotFound(string|null $message = null, int|null $code = null)
 *
 * @see \Illuminate\Auth\Access\Gate
 */
class Gate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GateContract::class;
    }
}
