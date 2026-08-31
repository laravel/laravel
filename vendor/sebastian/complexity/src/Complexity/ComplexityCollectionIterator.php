<?php declare(strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Complexity;

use Iterator;

/**
 * @template-implements Iterator<int, Complexity>
 */
final class ComplexityCollectionIterator implements Iterator
{
    /**
     * @var list<Complexity>
     */
    private readonly array $items;
    private int $position = 0;

    public function __construct(ComplexityCollection $items)
    {
        $this->items = $items->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Complexity
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
