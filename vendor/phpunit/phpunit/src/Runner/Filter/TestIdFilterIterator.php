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

use function in_array;
use PHPUnit\Event\TestData\NoDataSetFromDataProviderException;
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
final class TestIdFilterIterator extends RecursiveFilterIterator
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private readonly array $testIds;

    /**
     * @param RecursiveIterator<int, Test>     $iterator
     * @param non-empty-list<non-empty-string> $testIds
     */
    public function __construct(RecursiveIterator $iterator, array $testIds)
    {
        parent::__construct($iterator);

        $this->testIds = $testIds;
    }

    public function accept(): bool
    {
        $test = $this->getInnerIterator()->current();

        if ($test instanceof TestSuite) {
            return true;
        }

        if (!$test instanceof TestCase && !$test instanceof PhptTestCase) {
            return false;
        }

        try {
            return in_array($test->valueObjectForEvents()->id(), $this->testIds, true);
        } catch (NoDataSetFromDataProviderException) {
            return false;
        }
    }
}
