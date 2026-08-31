<?php

namespace Laravel\Prompts\Support;

use Closure;

/**
 * @internal
 */
class Utils
{
    /**
     * Determine if all items in an array match a truth test.
     *
     * @param  array<array-key, mixed>  $values
     */
    public static function allMatch(array $values, Closure $callback): bool
    {
        foreach ($values as $key => $value) {
            if (! $callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the last item from an array or null if it doesn't exist.
     *
     * @param  array<array-key, mixed>  $array
     */
    public static function last(array $array): mixed
    {
        return array_reverse($array)[0] ?? null;
    }

    /**
     * Returns the key of the first element in the array that satisfies the callback.
     *
     * @param  array<array-key, mixed>  $array
     */
    public static function search(array $array, Closure $callback): int|string|false
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return false;
    }
}
