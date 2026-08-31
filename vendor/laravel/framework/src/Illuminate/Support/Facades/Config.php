<?php

namespace Illuminate\Support\Facades;

/**
 * @method static bool has(string $key)
 * @method static mixed get(array|string $key, mixed $default = null)
 * @method static array getMany(array $keys)
 * @method static string string(string $key, \Closure|string|null $default = null)
 * @method static int integer(string $key, \Closure|int|null $default = null)
 * @method static float float(string $key, \Closure|float|null $default = null)
 * @method static bool boolean(string $key, \Closure|bool|null $default = null)
 * @method static array array(string $key, \Closure|array|null $default = null)
 * @method static \Illuminate\Support\Collection collection(string $key, \Closure|array|null $default = null)
 * @method static void set(array|string $key, mixed $value = null)
 * @method static void prepend(string $key, mixed $value)
 * @method static void push(string $key, mixed $value)
 * @method static array all()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Illuminate\Config\Repository
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}
