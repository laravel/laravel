<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Provider\Node;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;

/**
 * A collection of NodeProviderInterface objects
 *
 * @deprecated this class has been deprecated and will be removed in 5.0.0. The use-case for this class comes from a
 *     pre-`phpstan/phpstan` and pre-`vimeo/psalm` ecosystem, in which type safety had to be mostly enforced at runtime:
 *     that is no longer necessary, now that you can safely verify your code to be correct and use more generic types
 *     like `iterable<T>` instead.
 *
 * @extends AbstractCollection<NodeProviderInterface>
 */
class NodeProviderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return NodeProviderInterface::class;
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into a UuidInterface instance
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, NodeProviderInterface> $data */
        $data = unserialize($serialized, [
            'allowed_classes' => [
                Hexadecimal::class,
                RandomNodeProvider::class,
                StaticNodeProvider::class,
                SystemNodeProvider::class,
            ],
        ]);

        /** @phpstan-ignore-next-line */
        $this->data = array_filter($data, fn ($unserialized): bool => $unserialized instanceof NodeProviderInterface);
    }
}
