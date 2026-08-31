<?php

namespace Illuminate\Support\Facades;

/**
 * @method static \Illuminate\Hashing\BcryptHasher createBcryptDriver()
 * @method static \Illuminate\Hashing\ArgonHasher createArgonDriver()
 * @method static \Illuminate\Hashing\Argon2IdHasher createArgon2idDriver()
 * @method static array info(string $hashedValue)
 * @method static string make(string $value, array $options = [])
 * @method static bool check(string $value, string $hashedValue, array $options = [])
 * @method static bool needsRehash(string $hashedValue, array $options = [])
 * @method static bool isHashed(string $value)
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \Illuminate\Hashing\HashManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \Illuminate\Contracts\Container\Container getContainer()
 * @method static \Illuminate\Hashing\HashManager setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Illuminate\Hashing\HashManager forgetDrivers()
 *
 * @see \Illuminate\Hashing\HashManager
 * @see \Illuminate\Hashing\AbstractHasher
 */
class Hash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hash';
    }
}
