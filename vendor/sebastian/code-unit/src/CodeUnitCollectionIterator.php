<?php declare(strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeUnit;

use Iterator;

/**
 * @template-implements Iterator<int, CodeUnit>
 */
final class CodeUnitCollectionIterator implements Iterator
{
    /**
     * @var list<CodeUnit>
     */
    private array $codeUnits;
    private int $position = 0;

    public function __construct(CodeUnitCollection $collection)
    {
        $this->codeUnits = $collection->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->codeUnits[$this->position]);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): CodeUnit
    {
        return $this->codeUnits[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
