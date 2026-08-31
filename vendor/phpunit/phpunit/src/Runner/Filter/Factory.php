<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner\Filter;

use function assert;
use FilterIterator;
use Iterator;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Factory
{
    /**
     * @var list<array{className: class-string<FilterIterator<int, Test, Iterator<int, Test>>>, argument: list<non-empty-string>|non-empty-string}>
     */
    private array $filters = [];

    /**
     * @param list<non-empty-string> $testIds
     */
    public function addTestIdFilter(array $testIds): void
    {
        $this->filters[] = [
            'className' => TestIdFilterIterator::class,
            'argument'  => $testIds,
        ];
    }

    /**
     * @param list<non-empty-string> $groups
     */
    public function addIncludeGroupFilter(array $groups): void
    {
        $this->filters[] = [
            'className' => IncludeGroupFilterIterator::class,
            'argument'  => $groups,
        ];
    }

    /**
     * @param list<non-empty-string> $groups
     */
    public function addExcludeGroupFilter(array $groups): void
    {
        $this->filters[] = [
            'className' => ExcludeGroupFilterIterator::class,
            'argument'  => $groups,
        ];
    }

    /**
     * @param non-empty-string $name
     */
    public function addIncludeNameFilter(string $name): void
    {
        $this->filters[] = [
            'className' => IncludeNameFilterIterator::class,
            'argument'  => $name,
        ];
    }

    /**
     * @param non-empty-string $name
     */
    public function addExcludeNameFilter(string $name): void
    {
        $this->filters[] = [
            'className' => ExcludeNameFilterIterator::class,
            'argument'  => $name,
        ];
    }

    /**
     * @param Iterator<int, Test> $iterator
     *
     * @return FilterIterator<int, Test, Iterator<int, Test>>
     */
    public function factory(Iterator $iterator, TestSuite $suite): FilterIterator
    {
        foreach ($this->filters as $filter) {
            $iterator = new $filter['className'](
                $iterator,
                $filter['argument'],
                $suite,
            );
        }

        assert($iterator instanceof FilterIterator);

        return $iterator;
    }
}
