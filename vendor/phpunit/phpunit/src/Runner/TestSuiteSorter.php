<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Runner;

use function array_diff;
use function array_merge;
use function array_reverse;
use function array_splice;
use function assert;
use function count;
use function in_array;
use function max;
use function shuffle;
use function usort;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\Reorderable;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\ResultCache\NullResultCache;
use PHPUnit\Runner\ResultCache\ResultCache;
use PHPUnit\Runner\ResultCache\ResultCacheId;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestSuiteSorter
{
    /**
     * @var int
     */
    public const ORDER_DEFAULT = 0;

    /**
     * @var int
     */
    public const ORDER_RANDOMIZED = 1;

    /**
     * @var int
     */
    public const ORDER_REVERSED = 2;

    /**
     * @var int
     */
    public const ORDER_DEFECTS_FIRST = 3;

    /**
     * @var int
     */
    public const ORDER_DURATION = 4;

    /**
     * @var int
     */
    public const ORDER_SIZE = 5;

    private const SIZE_SORT_WEIGHT = [
        'small'   => 1,
        'medium'  => 2,
        'large'   => 3,
        'unknown' => 4,
    ];

    /**
     * @var array<string, int> Associative array of (string => DEFECT_SORT_WEIGHT) elements
     */
    private array $defectSortOrder = [];
    private readonly ResultCache $cache;

    public function __construct(?ResultCache $cache = null)
    {
        $this->cache = $cache ?? new NullResultCache;
    }

    /**
     * @throws Exception
     */
    public function reorderTestsInSuite(Test $suite, int $order, bool $resolveDependencies, int $orderDefects): void
    {
        $allowedOrders = [
            self::ORDER_DEFAULT,
            self::ORDER_REVERSED,
            self::ORDER_RANDOMIZED,
            self::ORDER_DURATION,
            self::ORDER_SIZE,
        ];

        if (!in_array($order, $allowedOrders, true)) {
            // @codeCoverageIgnoreStart
            throw new InvalidOrderException;
            // @codeCoverageIgnoreEnd
        }

        $allowedOrderDefects = [
            self::ORDER_DEFAULT,
            self::ORDER_DEFECTS_FIRST,
        ];

        if (!in_array($orderDefects, $allowedOrderDefects, true)) {
            // @codeCoverageIgnoreStart
            throw new InvalidOrderException;
            // @codeCoverageIgnoreEnd
        }

        if ($suite instanceof TestSuite) {
            foreach ($suite as $_suite) {
                $this->reorderTestsInSuite($_suite, $order, $resolveDependencies, $orderDefects);
            }

            if ($orderDefects === self::ORDER_DEFECTS_FIRST) {
                $this->addSuiteToDefectSortOrder($suite);
            }

            $this->sort($suite, $order, $resolveDependencies, $orderDefects);
        }
    }

    private function sort(TestSuite $suite, int $order, bool $resolveDependencies, int $orderDefects): void
    {
        if (empty($suite->tests())) {
            return;
        }

        if ($order === self::ORDER_REVERSED) {
            $suite->setTests($this->reverse($suite->tests()));
        } elseif ($order === self::ORDER_RANDOMIZED) {
            $suite->setTests($this->randomize($suite->tests()));
        } elseif ($order === self::ORDER_DURATION) {
            $suite->setTests($this->sortByDuration($suite->tests()));
        } elseif ($order === self::ORDER_SIZE) {
            $suite->setTests($this->sortBySize($suite->tests()));
        }

        if ($orderDefects === self::ORDER_DEFECTS_FIRST) {
            $suite->setTests($this->sortDefectsFirst($suite->tests()));
        }

        if ($resolveDependencies && !($suite instanceof DataProviderTestSuite)) {
            $tests = $suite->tests();

            /** @noinspection PhpParamsInspection */
            /** @phpstan-ignore argument.type */
            $suite->setTests($this->resolveDependencies($tests));
        }
    }

    private function addSuiteToDefectSortOrder(TestSuite $suite): void
    {
        $max = 0;

        foreach ($suite->tests() as $test) {
            if (!$test instanceof Reorderable) {
                continue;
            }

            $sortId = $test->sortId();

            if (!isset($this->defectSortOrder[$sortId])) {
                $this->defectSortOrder[$sortId] = $this->cache->status(ResultCacheId::fromReorderable($test))->asInt();
                $max                            = max($max, $this->defectSortOrder[$sortId]);
            }
        }

        $this->defectSortOrder[$suite->sortId()] = $max;
    }

    /**
     * @param list<Test> $tests
     *
     * @return list<Test>
     */
    private function reverse(array $tests): array
    {
        return array_reverse($tests);
    }

    /**
     * @param list<Test> $tests
     *
     * @return list<Test>
     */
    private function randomize(array $tests): array
    {
        shuffle($tests);

        return $tests;
    }

    /**
     * @param list<Test> $tests
     *
     * @return list<Test>
     */
    private function sortDefectsFirst(array $tests): array
    {
        usort(
            $tests,
            fn ($left, $right) => $this->cmpDefectPriorityAndTime($left, $right),
        );

        return $tests;
    }

    /**
     * @param list<Test> $tests
     *
     * @return list<Test>
     */
    private function sortByDuration(array $tests): array
    {
        usort(
            $tests,
            fn ($left, $right) => $this->cmpDuration($left, $right),
        );

        return $tests;
    }

    /**
     * @param list<Test> $tests
     *
     * @return list<Test>
     */
    private function sortBySize(array $tests): array
    {
        usort(
            $tests,
            fn ($left, $right) => $this->cmpSize($left, $right),
        );

        return $tests;
    }

    /**
     * Comparator callback function to sort tests for "reach failure as fast as possible".
     *
     * 1. sort tests by defect weight defined in self::DEFECT_SORT_WEIGHT
     * 2. when tests are equally defective, sort the fastest to the front
     * 3. do not reorder successful tests
     */
    private function cmpDefectPriorityAndTime(Test $a, Test $b): int
    {
        assert($a instanceof Reorderable);
        assert($b instanceof Reorderable);

        $priorityA = $this->defectSortOrder[$a->sortId()] ?? 0;
        $priorityB = $this->defectSortOrder[$b->sortId()] ?? 0;

        if ($priorityB <=> $priorityA) {
            // Sort defect weight descending
            return $priorityB <=> $priorityA;
        }

        if ($priorityA || $priorityB) {
            return $this->cmpDuration($a, $b);
        }

        // do not change execution order
        return 0;
    }

    /**
     * Compares test duration for sorting tests by duration ascending.
     */
    private function cmpDuration(Test $a, Test $b): int
    {
        if (!($a instanceof Reorderable && $b instanceof Reorderable)) {
            return 0;
        }

        return $this->cache->time(ResultCacheId::fromReorderable($a)) <=> $this->cache->time(ResultCacheId::fromReorderable($b));
    }

    /**
     * Compares test size for sorting tests small->medium->large->unknown.
     */
    private function cmpSize(Test $a, Test $b): int
    {
        $sizeA = ($a instanceof TestCase || $a instanceof DataProviderTestSuite)
            ? $a->size()->asString()
            : 'unknown';
        $sizeB = ($b instanceof TestCase || $b instanceof DataProviderTestSuite)
            ? $b->size()->asString()
            : 'unknown';

        return self::SIZE_SORT_WEIGHT[$sizeA] <=> self::SIZE_SORT_WEIGHT[$sizeB];
    }

    /**
     * Reorder Tests within a TestCase in such a way as to resolve as many dependencies as possible.
     * The algorithm will leave the tests in original running order when it can.
     * For more details see the documentation for test dependencies.
     *
     * Short description of algorithm:
     * 1. Pick the next Test from remaining tests to be checked for dependencies.
     * 2. If the test has no dependencies: mark done, start again from the top
     * 3. If the test has dependencies but none left to do: mark done, start again from the top
     * 4. When we reach the end add any leftover tests to the end. These will be marked 'skipped' during execution.
     *
     * @param array<DataProviderTestSuite|TestCase> $tests
     *
     * @return array<DataProviderTestSuite|TestCase>
     */
    private function resolveDependencies(array $tests): array
    {
        $newTestOrder = [];
        $i            = 0;
        $provided     = [];

        do {
            if ([] === array_diff($tests[$i]->requires(), $provided)) {
                $provided     = array_merge($provided, $tests[$i]->provides());
                $newTestOrder = array_merge($newTestOrder, array_splice($tests, $i, 1));
                $i            = 0;
            } else {
                $i++;
            }
        } while (!empty($tests) && ($i < count($tests)));

        return array_merge($newTestOrder, $tests);
    }
}
