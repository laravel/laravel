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

use function array_merge;
use function array_push;
use function in_array;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\PhptTestCase;
use RecursiveFilterIterator;
use RecursiveIterator;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class GroupFilterIterator extends RecursiveFilterIterator
{
    /**
     * @var list<non-empty-string>
     */
    private readonly array $groupTests;

    /**
     * @param RecursiveIterator<int, Test> $iterator
     * @param list<non-empty-string>       $groups
     */
    public function __construct(RecursiveIterator $iterator, array $groups, TestSuite $suite)
    {
        parent::__construct($iterator);

        $groupTests = [];

        foreach ($suite->groups() as $group => $tests) {
            if (in_array($group, $groups, true)) {
                $groupTests = array_merge($groupTests, $tests);

                array_push($groupTests, ...$groupTests);
            }
        }

        $this->groupTests = $groupTests;
    }

    public function accept(): bool
    {
        $test = $this->getInnerIterator()->current();

        if ($test instanceof TestSuite) {
            return true;
        }

        if ($test instanceof TestCase || $test instanceof PhptTestCase) {
            return $this->doAccept($test->valueObjectForEvents()->id(), $this->groupTests);
        }

        return true;
    }

    /**
     * @param non-empty-string       $id
     * @param list<non-empty-string> $groupTests
     */
    abstract protected function doAccept(string $id, array $groupTests): bool;
}
