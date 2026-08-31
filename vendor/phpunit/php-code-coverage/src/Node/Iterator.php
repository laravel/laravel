<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

use function count;
use RecursiveIterator;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Iterator implements RecursiveIterator
{
    private int $position;

    /**
     * @var list<AbstractNode>
     */
    private readonly array $nodes;

    public function __construct(Directory $node)
    {
        $this->nodes = $node->children();
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Checks if there is a current element after calls to rewind() or next().
     */
    public function valid(): bool
    {
        return $this->position < count($this->nodes);
    }

    /**
     * Returns the key of the current element.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Returns the current element.
     */
    public function current(): ?AbstractNode
    {
        return $this->valid() ? $this->nodes[$this->position] : null;
    }

    /**
     * Moves forward to next element.
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * Returns the sub iterator for the current element.
     */
    public function getChildren(): self
    {
        return new self($this->nodes[$this->position]);
    }

    /**
     * Checks whether the current element has children.
     */
    public function hasChildren(): bool
    {
        return $this->nodes[$this->position] instanceof Directory;
    }
}
