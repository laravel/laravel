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

use function array_merge;
use function count;
use Countable;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, CodeUnit>
 *
 * @immutable
 */
final readonly class CodeUnitCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<CodeUnit>
     */
    private array $codeUnits;

    public static function fromList(CodeUnit ...$codeUnits): self
    {
        // @phpstan-ignore argument.type
        return new self($codeUnits);
    }

    /**
     * @param list<CodeUnit> $codeUnits
     */
    private function __construct(array $codeUnits)
    {
        $this->codeUnits = $codeUnits;
    }

    /**
     * @return list<CodeUnit>
     */
    public function asArray(): array
    {
        return $this->codeUnits;
    }

    public function getIterator(): CodeUnitCollectionIterator
    {
        return new CodeUnitCollectionIterator($this);
    }

    public function count(): int
    {
        return count($this->codeUnits);
    }

    public function isEmpty(): bool
    {
        return $this->codeUnits === [];
    }

    public function mergeWith(self $other): self
    {
        return new self(
            array_merge(
                $this->asArray(),
                $other->asArray(),
            ),
        );
    }
}
