<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Logging\TestDox;

use function count;
use Iterator;

/**
 * @template-implements Iterator<non-negative-int, TestResult>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestResultCollectionIterator implements Iterator
{
    /**
     * @var list<TestResult>
     */
    private readonly array $testResults;

    /**
     * @var non-negative-int
     */
    private int $position = 0;

    public function __construct(TestResultCollection $testResults)
    {
        $this->testResults = $testResults->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->testResults);
    }

    /**
     * @return non-negative-int
     */
    public function key(): int
    {
        return $this->position;
    }

    public function current(): TestResult
    {
        return $this->testResults[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
