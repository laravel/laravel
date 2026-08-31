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
use function floor;
use function sprintf;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

final class DateTimeComparator extends ObjectComparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return ($expected instanceof DateTime || $expected instanceof DateTimeImmutable) &&
               ($actual instanceof DateTime || $actual instanceof DateTimeImmutable);
    }

    /**
     * @param array<mixed> $processed
     *
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false, array &$processed = []): void
    {
        assert($expected instanceof DateTime || $expected instanceof DateTimeImmutable);
        assert($actual instanceof DateTime || $actual instanceof DateTimeImmutable);

        $absDelta = abs($delta);
        $delta    = new DateInterval(sprintf('PT%dS', $absDelta));
        $delta->f = $absDelta - floor($absDelta);

        $actualClone = (clone $actual)
            ->setTimezone(new DateTimeZone('UTC'));

        $expectedLower = (clone $expected)
            ->setTimezone(new DateTimeZone('UTC'))
            ->sub($delta);

        $expectedUpper = (clone $expected)
            ->setTimezone(new DateTimeZone('UTC'))
            ->add($delta);

        if ($actualClone < $expectedLower || $actualClone > $expectedUpper) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                $expected->format('Y-m-d\TH:i:s.uO'),
                $actual->format('Y-m-d\TH:i:s.uO'),
                'Failed asserting that two DateTime objects are equal.',
            );
        }
    }
}
