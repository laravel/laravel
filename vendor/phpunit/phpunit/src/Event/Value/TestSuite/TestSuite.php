<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Code\TestCollection;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
abstract readonly class TestSuite
{
    /**
     * @var non-empty-string
     */
    private string $name;
    private int $count;
    private TestCollection $tests;

    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name, int $size, TestCollection $tests)
    {
        $this->name  = $name;
        $this->count = $size;
        $this->tests = $tests;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function count(): int
    {
        return $this->count;
    }

    public function tests(): TestCollection
    {
        return $this->tests;
    }

    /**
     * @phpstan-assert-if-true TestSuiteWithName $this
     */
    public function isWithName(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true TestSuiteForTestClass $this
     */
    public function isForTestClass(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true TestSuiteForTestMethodWithDataProvider $this
     */
    public function isForTestMethodWithDataProvider(): bool
    {
        return false;
    }
}
