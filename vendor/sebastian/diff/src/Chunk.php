<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Diff;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template-implements IteratorAggregate<int, Line>
 */
final class Chunk implements IteratorAggregate
{
    private int $start;
    private int $startRange;
    private int $end;
    private int $endRange;

    /**
     * @var list<Line>
     */
    private array $lines;

    /**
     * @param list<Line> $lines
     */
    public function __construct(int $start = 0, int $startRange = 1, int $end = 0, int $endRange = 1, array $lines = [])
    {
        $this->start      = $start;
        $this->startRange = $startRange;
        $this->end        = $end;
        $this->endRange   = $endRange;
        $this->lines      = $lines;
    }

    public function start(): int
    {
        return $this->start;
    }

    public function startRange(): int
    {
        return $this->startRange;
    }

    public function end(): int
    {
        return $this->end;
    }

    public function endRange(): int
    {
        return $this->endRange;
    }

    /**
     * @return list<Line>
     */
    public function lines(): array
    {
        return $this->lines;
    }

    /**
     * @param list<Line> $lines
     */
    public function setLines(array $lines): void
    {
        foreach ($lines as $line) {
            if (!$line instanceof Line) {
                throw new InvalidArgumentException;
            }
        }

        $this->lines = $lines;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->lines);
    }
}
