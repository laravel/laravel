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

namespace Ramsey\Collection;

/**
 * A set is a collection that contains no duplicate elements.
 *
 * Great care must be exercised if mutable objects are used as set elements.
 * The behavior of a set is not specified if the value of an object is changed
 * in a manner that affects equals comparisons while the object is an element in
 * the set.
 *
 * Example usage:
 *
 * ```
 * $foo = new \My\Foo();
 * $set = new Set(\My\Foo::class);
 *
 * $set->add($foo); // returns TRUE, the element doesn't exist
 * $set->add($foo); // returns FALSE, the element already exists
 *
 * $bar = new \My\Foo();
 * $set->add($bar); // returns TRUE, $bar !== $foo
 * ```
 *
 * @template T
 * @extends AbstractSet<T>
 */
class Set extends AbstractSet
{
    /**
     * Constructs a set object of the specified type, optionally with the
     * specified data.
     *
     * @param string $setType The type or class name associated with this set.
     * @param array<array-key, T> $data The initial items to store in the set.
     */
    public function __construct(private readonly string $setType, array $data = [])
    {
        parent::__construct($data);
    }

    public function getType(): string
    {
        return $this->setType;
    }
}
