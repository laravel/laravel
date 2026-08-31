<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use Exception;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Exception for expectations which failed their check.
 *
 * The exception contains the error message and optionally a
 * SebastianBergmann\Comparator\ComparisonFailure which is used to
 * generate diff output of the failed expectations.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ExpectationFailedException extends AssertionFailedError
{
    protected ?ComparisonFailure $comparisonFailure = null;

    public function __construct(string $message, ?ComparisonFailure $comparisonFailure = null, ?Exception $previous = null)
    {
        $this->comparisonFailure = $comparisonFailure;

        parent::__construct($message, 0, $previous);
    }

    public function getComparisonFailure(): ?ComparisonFailure
    {
        return $this->comparisonFailure;
    }
}
