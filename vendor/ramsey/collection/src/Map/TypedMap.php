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

/**
 * A `TypedMap` represents a map of elements where key and value are typed.
 *
 * Each element is identified by a key with defined type and a value of defined
 * type. The keys of the map must be unique. The values on the map can be
 * repeated but each with its own different key.
 *
 * The most common case is to use a string type key, but it's not limited to
 * this type of keys.
 *
 * This is a direct implementation of `TypedMapInterface`, provided for the sake
 * of convenience.
 *
 * Example usage:
 *
 * ```
 * $map = new TypedMap('string', Foo::class);
 * $map['x'] = new Foo();
 * foreach ($map as $key => $value) {
 *     // do something with $key, it will be a Foo::class
 * }
 *
 * // this will throw an exception since key must be string
 * $map[10] = new Foo();
 *
 * // this will throw an exception since value must be a Foo
 * $map['bar'] = 'bar';
 *
 * // initialize map with contents
 * $map = new TypedMap('string', Foo::class, [
 *     new Foo(), new Foo(), new Foo()
 * ]);
 * ```
 *
 * It is preferable to subclass `AbstractTypedMap` to create your own typed map
 * implementation:
 *
 * ```
 * class FooTypedMap extends AbstractTypedMap
 * {
 *     public function getKeyType()
 *     {
 *         return 'int';
 *     }
 *
 *     public function getValueType()
 *     {
 *          return Foo::class;
 *     }
 * }
 * ```
 *
 * … but you also may use the `TypedMap` class:
 *
 * ```
 * class FooTypedMap extends TypedMap
 * {
 *     public function __constructor(array $data = [])
 *     {
 *         parent::__construct('int', Foo::class, $data);
 *     }
 * }
 * ```
 *
 * @template K of array-key
 * @template T
 * @extends AbstractTypedMap<K, T>
 */
class TypedMap extends AbstractTypedMap
{
    /**
     * Constructs a map object of the specified key and value types,
     * optionally with the specified data.
     *
     * @param string $keyType The data type of the map's keys.
     * @param string $valueType The data type of the map's values.
     * @param array<K, T> $data The initial data to set for this map.
     */
    public function __construct(
        private readonly string $keyType,
        private readonly string $valueType,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function getKeyType(): string
    {
        return $this->keyType;
    }

    public function getValueType(): string
    {
        return $this->valueType;
    }
}
