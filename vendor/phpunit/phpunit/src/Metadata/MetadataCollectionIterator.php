<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata;

use function count;
use Iterator;

/**
 * @template-implements Iterator<non-negative-int, Metadata>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class MetadataCollectionIterator implements Iterator
{
    /**
     * @var list<Metadata>
     */
    private readonly array $metadata;

    /**
     * @var non-negative-int
     */
    private int $position = 0;

    public function __construct(MetadataCollection $metadata)
    {
        $this->metadata = $metadata->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->metadata);
    }

    /**
     * @return non-negative-int
     */
    public function key(): int
    {
        return $this->position;
    }

    public function current(): Metadata
    {
        return $this->metadata[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
