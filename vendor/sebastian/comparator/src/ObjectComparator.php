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
use function in_array;
use function is_object;
use function sprintf;
use function substr_replace;
use SebastianBergmann\Exporter\Exporter;

class ObjectComparator extends ArrayComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return is_object($expected) && is_object($actual);
    }

    /**
     * @param array<mixed> $processed
     *
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false, array &$processed = []): void
    {
        assert(is_object($expected));
        assert(is_object($actual));

        if ($actual::class !== $expected::class) {
            $exporter = new Exporter;

            throw new ComparisonFailure(
                $expected,
                $actual,
                $exporter->export($expected),
                $exporter->export($actual),
                sprintf(
                    '%s is not instance of expected class "%s".',
                    $exporter->export($actual),
                    $expected::class,
                ),
            );
        }

        // don't compare twice to allow for cyclic dependencies
        if (in_array([$actual, $expected], $processed, true) ||
            in_array([$expected, $actual], $processed, true)) {
            return;
        }

        $processed[] = [$actual, $expected];

        // don't compare objects if they are identical
        // this helps to avoid the error "maximum function nesting level reached"
        // CAUTION: this conditional clause is not tested
        if ($actual !== $expected) {
            try {
                parent::assertEquals(
                    $this->toArray($expected),
                    $this->toArray($actual),
                    $delta,
                    $canonicalize,
                    $ignoreCase,
                    $processed,
                );
            } catch (ComparisonFailure $e) {
                throw new ComparisonFailure(
                    $expected,
                    $actual,
                    // replace "Array" with "MyClass object"
                    substr_replace($e->getExpectedAsString(), $expected::class . ' Object', 0, 5),
                    substr_replace($e->getActualAsString(), $actual::class . ' Object', 0, 5),
                    'Failed asserting that two objects are equal.',
                );
            }
        }
    }

    /**
     * @return array<mixed>
     */
    protected function toArray(object $object): array
    {
        return (new Exporter)->toArray($object);
    }
}
