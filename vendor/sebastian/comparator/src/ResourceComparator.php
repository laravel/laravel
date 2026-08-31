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
use function is_resource;
use SebastianBergmann\Exporter\Exporter;

final class ResourceComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return is_resource($expected) && is_resource($actual);
    }

    /**
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        assert(is_resource($expected));
        assert(is_resource($actual));

        $exporter = new Exporter;

        if ($actual != $expected) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $exporter->export($expected),
                $exporter->export($actual),
            );
        }
    }
}
