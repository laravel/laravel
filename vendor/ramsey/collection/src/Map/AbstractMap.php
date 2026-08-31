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

use Ramsey\Collection\AbstractArray;
use Ramsey\Collection\Exception\InvalidArgumentException;
use Traversable;

use function array_key_exists;
use function array_keys;
use function in_array;
use function var_export;

/**
 * This class provides a basic implementation of `MapInterface`, to minimize the
 * effort required to implement this interface.
 *
 * @template K of array-key
 * @template T
 * @extends AbstractArray<T>
 * @implements MapInterface<K, T>
 */
abstract class AbstractMap extends AbstractArray implements MapInterface
{
    /**
     * @param array<K, T> $data The initial items to add to this map.
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @return Traversable<K, T>
     */
    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }

    /**
     * @param K $offset The offset to set
     * @param T $value The value to set at the given offset.
     *
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            throw new InvalidArgumentException(
                'Map elements are key/value pairs; a key must be provided for '
                . 'value ' . var_export($value, true),
            );
        }

        $this->data[$offset] = $value;
    }

    public function containsKey(int | string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function containsValue(mixed $value): bool
    {
        return in_array($value, $this->data, true);
    }

    /**
     * @inheritDoc
     */
    public function keys(): array
    {
        /** @var list<K> */
        return array_keys($this->data);
    }

    /**
     * @param K $key The key to return from the map.
     * @param T | null $defaultValue The default value to use if `$key` is not found.
     *
     * @return T | null the value or `null` if the key could not be found.
     */
    public function get(int | string $key, mixed $defaultValue = null): mixed
    {
        return $this[$key] ?? $defaultValue;
    }

    /**
     * @param K $key The key to put or replace in the map.
     * @param T $value The value to store at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function put(int | string $key, mixed $value): mixed
    {
        $previousValue = $this->get($key);
        $this[$key] = $value;

        return $previousValue;
    }

    /**
     * @param K $key The key to put in the map.
     * @param T $value The value to store at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function putIfAbsent(int | string $key, mixed $value): mixed
    {
        $currentValue = $this->get($key);

        if ($currentValue === null) {
            $this[$key] = $value;
        }

        return $currentValue;
    }

    /**
     * @param K $key The key to remove from the map.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function remove(int | string $key): mixed
    {
        $previousValue = $this->get($key);
        unset($this[$key]);

        return $previousValue;
    }

    public function removeIf(int | string $key, mixed $value): bool
    {
        if ($this->get($key) === $value) {
            unset($this[$key]);

            return true;
        }

        return false;
    }

    /**
     * @param K $key The key to replace.
     * @param T $value The value to set at `$key`.
     *
     * @return T | null the previous value associated with key, or `null` if
     *     there was no mapping for `$key`.
     */
    public function replace(int | string $key, mixed $value): mixed
    {
        $currentValue = $this->get($key);

        if ($this->containsKey($key)) {
            $this[$key] = $value;
        }

        return $currentValue;
    }

    public function replaceIf(int | string $key, mixed $oldValue, mixed $newValue): bool
    {
        if ($this->get($key) === $oldValue) {
            $this[$key] = $newValue;

            return true;
        }

        return false;
    }

    /**
     * @return array<K, T>
     */
    public function __serialize(): array
    {
        /** @var array<K, T> */
        return parent::__serialize();
    }

    /**
     * @return array<K, T>
     */
    public function toArray(): array
    {
        /** @var array<K, T> */
        return parent::toArray();
    }
}
