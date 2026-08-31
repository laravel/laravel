<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use function gettype;
use function sprintf;
use SebastianBergmann\Exporter\Exporter;

final class TypeComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return true;
    }

    /**
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        if (gettype($expected) != gettype($actual)) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                // we don't need a diff
                '',
                '',
                sprintf(
                    '%s does not match expected type "%s".',
                    (new Exporter)->shortenedExport($actual),
                    gettype($expected),
                ),
            );
        }
    }
}
