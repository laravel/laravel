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

namespace Ramsey\Uuid\Builder;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Uuid\Converter\Number\GenericNumberConverter;
use Ramsey\Uuid\Converter\Time\GenericTimeConverter;
use Ramsey\Uuid\Converter\Time\PhpTimeConverter;
use Ramsey\Uuid\Guid\GuidBuilder;
use Ramsey\Uuid\Math\BrickMathCalculator;
use Ramsey\Uuid\Nonstandard\UuidBuilder as NonstandardUuidBuilder;
use Ramsey\Uuid\Rfc4122\UuidBuilder as Rfc4122UuidBuilder;
use Traversable;

/**
 * A collection of UuidBuilderInterface objects
 *
 * @deprecated this class has been deprecated and will be removed in 5.0.0. The use-case for this class comes from a
 *     pre-`phpstan/phpstan` and pre-`vimeo/psalm` ecosystem, in which type safety had to be mostly enforced at runtime:
 *     that is no longer necessary, now that you can safely verify your code to be correct, and use more generic types
 *     like `iterable<T>` instead.
 *
 * @extends AbstractCollection<UuidBuilderInterface>
 */
class BuilderCollection extends AbstractCollection
{
    public function getType(): string
    {
        return UuidBuilderInterface::class;
    }

    public function getIterator(): Traversable
    {
        return parent::getIterator();
    }

    /**
     * Re-constructs the object from its serialized form
     *
     * @param string $serialized The serialized PHP string to unserialize into a UuidInterface instance
     */
    public function unserialize($serialized): void
    {
        /** @var array<array-key, UuidBuilderInterface> $data */
        $data = unserialize($serialized, [
            'allowed_classes' => [
                BrickMathCalculator::class,
                GenericNumberConverter::class,
                GenericTimeConverter::class,
                GuidBuilder::class,
                NonstandardUuidBuilder::class,
                PhpTimeConverter::class,
                Rfc4122UuidBuilder::class,
            ],
        ]);

        $this->data = array_filter(
            $data,
            function ($unserialized): bool {
                /** @phpstan-ignore instanceof.alwaysTrue */
                return $unserialized instanceof UuidBuilderInterface;
            },
        );
    }
}
