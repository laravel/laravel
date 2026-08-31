<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code;

use function count;
use Countable;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<non-negative-int, Test>
 *
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<Test>
     */
    private array $tests;

    /**
     * @param list<Test> $tests
     */
    public static function fromArray(array $tests): self
    {
        return new self(...$tests);
    }

    private function __construct(Test ...$tests)
    {
        $this->tests = $tests;
    }

    /**
     * @return list<Test>
     */
    public function asArray(): array
    {
        return $this->tests;
    }

    public function count(): int
    {
        return count($this->tests);
    }

    public function getIterator(): TestCollectionIterator
    {
        return new TestCollectionIterator($this);
    }
}
