<?php

namespace Laravel\Prompts\Concerns;

use Closure;
use RuntimeException;

trait Fallback
{
    /**
     * Whether to fallback to a custom implementation
     */
    protected static bool $shouldFallback = false;

    /**
     * The fallback implementations.
     *
     * @var array<class-string, Closure($this): mixed>
     */
    protected static array $fallbacks = [];

    /**
     * Enable the fallback implementation.
     */
    public static function fallbackWhen(bool $condition): void
    {
        static::$shouldFallback = $condition || static::$shouldFallback;
    }

    /**
     * Whether the prompt should fallback to a custom implementation.
     */
    public static function shouldFallback(): bool
    {
        return static::$shouldFallback && isset(static::$fallbacks[static::class]);
    }

    /**
     * Set the fallback implementation.
     *
     * @param  Closure($this): mixed  $fallback
     */
    public static function fallbackUsing(Closure $fallback): void
    {
        static::$fallbacks[static::class] = $fallback;
    }

    /**
     * Call the registered fallback implementation.
     */
    public function fallback(): mixed
    {
        $fallback = static::$fallbacks[static::class] ?? null;

        if ($fallback === null) {
            throw new RuntimeException('No fallback implementation registered for ['.static::class.']');
        }

        return $fallback($this);
    }
}
