<?php

/**
 * This file is part of the ramsey/collection library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Collection\Map;

use Ramsey\Collection\ArrayInterface;

/**
 * An object that maps keys to values.
 *
 * A map cannot contain duplicate keys; each key can map to at most one value.
 *
 * @template K of array-key
 * @template T
 * @extends ArrayInterface<T>
 */
interface MapInterface extends ArrayInterface
{
    /**
     * Returns `true` if this map contains a mapping for the specified key.
     *
     * @param K $key The key to check in the map.
     */
    public function containsKey(int | string $key): bool;

    /**
     * Returns `true` if this map maps one or more keys to the specified value.
     *
     * This performs a strict type check on the value.
     *
     * @param T $value The value to check in the map.
     */
    public function containsValue(mixed $value): bool;

    /**
     * Return an array of the keys contained in this map.
     *
     * @return list<K>
     */
    public function keys(): array;

    /**
     * Returns the value to which the specified key is mapped, `null` if this
     * map contains no mapping for the key, or (optionally) `$defaultValue` if
     * this map contains no mapping for the key.
     *
     * @param K $key The key to return from the map.
     * @param T | null $defaultValue The default value to use if `$key` is not found.
     *
     * @return T | null the value or `null` if the key could not be found.
     */
    public function get(int | string $key, mixed $defaultValue = null): mixed;

    /**
     * Associates the specified value with the specified key in this map.
     *
     * If the map previously contained a mapping for the key, the old value is
     * replaced by the specified value.
     *
     * @param K $key The key to put or replace in the map.
     * @param T $value The value to store at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function put(int | string $key, mixed $value): mixed;

    /**
     * Associates the specified value with the specified key in this map only if
     * it is not already set.
     *
     * If there is already a value associated with `$key`, this returns that
     * value without replacing it.
     *
     * @param K $key The key to put in the map.
     * @param T $value The value to store at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function putIfAbsent(int | string $key, mixed $value): mixed;

    /**
     * Removes the mapping for a key from this map if it is present.
     *
     * @param K $key The key to remove from the map.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function remove(int | string $key): mixed;

    /**
     * Removes the entry for the specified key only if it is currently mapped to
     * the specified value.
     *
     * This performs a strict type check on the value.
     *
     * @param K $key The key to remove from the map.
     * @param T $value The value to match.
     *
     * @return bool true if the value was removed.
     */
    public function removeIf(int | string $key, mixed $value): bool;

    /**
     * Replaces the entry for the specified key only if it is currently mapped
     * to some value.
     *
     * @param K $key The key to replace.
     * @param T $value The value to set at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function replace(int | string $key, mixed $value): mixed;

    /**
     * Replaces the entry for the specified key only if currently mapped to the
     * specified value.
     *
     * This performs a strict type check on the value.
     *
     * @param K $key The key to remove from the map.
     * @param T $oldValue The value to match.
     * @param T $newValue The value to use as a replacement.
     *
     * @return bool true if the value was replaced.
     */
    public function replaceIf(int | string $key, mixed $oldValue, mixed $newValue): bool;
}
