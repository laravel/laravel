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

use function abs;
use function assert;
use function is_float;
use function is_infinite;
use function is_nan;
use function is_numeric;
use function is_string;
use function sprintf;
use SebastianBergmann\Exporter\Exporter;

final class NumericComparator extends ScalarComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        // all numerical values, but not if both of them are strings
        return is_numeric($expected) && is_numeric($actual) &&
               !(is_string($expected) && is_string($actual));
    }

    /**
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        assert(is_numeric($expected));
        assert(is_numeric($actual));

        if ($this->isInfinite($expected) && $this->isInfinite($actual)) {
            if ($expected < 0 && $actual < 0) {
                return;
            }

            if ($expected > 0 && $actual > 0) {
                return;
            }
        }

        if (($this->isInfinite($actual) xor $this->isInfinite($expected)) ||
            ($this->isNan($actual) || $this->isNan($expected)) ||
            abs($actual - $expected) > $delta) {
            $exporter = new Exporter;

            throw new ComparisonFailure(
                $expected,
                $actual,
                '',
                '',
                sprintf(
                    'Failed asserting that %s matches expected %s.',
                    $exporter->export($actual),
                    $exporter->export($expected),
                ),
            );
        }
    }

    private function isInfinite(mixed $value): bool
    {
        return is_float($value) && is_infinite($value);
    }

    private function isNan(mixed $value): bool
    {
        return is_float($value) && is_nan($value);
    }
}
