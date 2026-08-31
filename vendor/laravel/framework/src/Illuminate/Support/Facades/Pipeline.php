<?php

namespace Illuminate\Support\Facades;

/**
 * @method static \Illuminate\Pipeline\Pipeline send(mixed $passable)
 * @method static \Illuminate\Pipeline\Pipeline through(mixed $pipes)
 * @method static \Illuminate\Pipeline\Pipeline pipe(mixed $pipes)
 * @method static \Illuminate\Pipeline\Pipeline via(string $method)
 * @method static mixed then(\Closure $destination)
 * @method static mixed thenReturn()
 * @method static \Illuminate\Pipeline\Pipeline finally(\Closure $callback)
 * @method static \Illuminate\Pipeline\Pipeline withinTransaction(string|null|\UnitEnum|false $withinTransaction = null)
 * @method static \Illuminate\Pipeline\Pipeline setContainer(\Illuminate\Contracts\Container\Container $container)
 * @method static \Illuminate\Pipeline\Pipeline|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Pipeline\Pipeline|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 *
 * @see \Illuminate\Pipeline\Pipeline
 */
class Pipeline extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pipeline';
    }
}
