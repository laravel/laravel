<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Reference;

final class MemoryLimitedReferenceMap implements ReferenceMapInterface
{
    private ReferenceMapInterface $decorated;

    private const MINIMUM_SIZE = 100_000;

    private int $remaining;

    public function __construct(ReferenceMapInterface $decorated, int $maxSize)
    {
        $this->decorated = $decorated;
        $this->remaining = \max(self::MINIMUM_SIZE, $maxSize);
    }

    public function add(ReferenceInterface $reference): void
    {
        $this->decorated->add($reference);
    }

    public function contains(string $label): bool
    {
        return $this->decorated->contains($label);
    }

    public function get(string $label): ?ReferenceInterface
    {
        $reference = $this->decorated->get($label);
        if ($reference === null) {
            return null;
        }

        // Check for expansion limit
        $this->remaining -= \strlen($reference->getDestination()) + \strlen($reference->getTitle());
        if ($this->remaining < 0) {
            return null;
        }

        return $reference;
    }

    /**
     * @return \Traversable<string, ReferenceInterface>
     */
    public function getIterator(): \Traversable
    {
        return $this->decorated->getIterator();
    }

    public function count(): int
    {
        return $this->decorated->count();
    }
}
