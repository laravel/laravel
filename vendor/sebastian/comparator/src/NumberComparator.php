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

use function assert;
use function is_int;
use function is_numeric;
use function is_string;
use function max;
use function number_format;
use BcMath\Number;

final class NumberComparator extends ObjectComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return ($expected instanceof Number || $actual instanceof Number) &&
            ($expected instanceof Number || is_int($expected) || is_string($expected) && is_numeric($expected)) &&
            ($actual instanceof Number || is_int($actual) || is_string($actual) && is_numeric($actual));
    }

    /**
     * @param array<mixed> $processed
     *
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false, array &$processed = []): void
    {
        if (!$expected instanceof Number) {
            assert(is_string($expected) || is_int($expected));

            $expected = new Number($expected);
        }

        if (!$actual instanceof Number) {
            assert(is_string($actual) || is_int($actual));

            $actual = new Number($actual);
        }

        $deltaNumber = new Number(number_format($delta, max($expected->scale, $actual->scale)));

        if ($actual < $expected - $deltaNumber || $actual > $expected + $deltaNumber) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                (string) $expected,
                (string) $actual,
                'Failed asserting that two Number objects are equal.',
            );
        }
    }
}
