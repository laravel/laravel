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

use function is_bool;
use function is_object;
use function is_scalar;
use function is_string;
use function mb_strtolower;
use function method_exists;
use function sprintf;
use function strlen;
use function substr;
use SebastianBergmann\Exporter\Exporter;

/**
 * Compares scalar or NULL values for equality.
 */
class ScalarComparator extends Comparator
{
    private const OVERLONG_THRESHOLD = 40;
    private const KEEP_CONTEXT_CHARS = 25;

    public function accepts(mixed $expected, mixed $actual): bool
    {
        return ((is_scalar($expected) xor null === $expected) &&
               (is_scalar($actual) xor null === $actual)) ||
               // allow comparison between strings and objects featuring __toString()
               (is_string($expected) && is_object($actual) && method_exists($actual, '__toString')) ||
               (is_object($expected) && method_exists($expected, '__toString') && is_string($actual));
    }

    /**
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        $expectedToCompare = $expected;
        $actualToCompare   = $actual;
        $exporter          = new Exporter;

        // always compare as strings to avoid strange behaviour
        // otherwise 0 == 'Foobar'
        if ((is_string($expected) && !is_bool($actual)) || (is_string($actual) && !is_bool($expected))) {
            /** @phpstan-ignore cast.string */
            $expectedToCompare = @(string) $expectedToCompare;

            /** @phpstan-ignore cast.string */
            $actualToCompare = @(string) $actualToCompare;

            if ($ignoreCase) {
                $expectedToCompare = mb_strtolower($expectedToCompare, 'UTF-8');
                $actualToCompare   = mb_strtolower($actualToCompare, 'UTF-8');
            }
        }

        if ($expectedToCompare !== $actualToCompare && is_string($expected) && is_string($actual)) {
            [$cutExpected, $cutActual] = self::removeOverlongCommonPrefix($expected, $actual);
            [$cutExpected, $cutActual] = self::removeOverlongCommonSuffix($cutExpected, $cutActual);

            throw new ComparisonFailure(
                $expected,
                $actual,
                $exporter->export($cutExpected),
                $exporter->export($cutActual),
                'Failed asserting that two strings are equal.',
            );
        }

        if ($expectedToCompare != $actualToCompare) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                // no diff is required
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

    /**
     * @return array{string, string}
     */
    private static function removeOverlongCommonPrefix(string $string1, string $string2): array
    {
        $commonPrefix = self::findCommonPrefix($string1, $string2);

        if (strlen($commonPrefix) > self::OVERLONG_THRESHOLD) {
            $string1 = '...' . substr($string1, strlen($commonPrefix) - self::KEEP_CONTEXT_CHARS);
            $string2 = '...' . substr($string2, strlen($commonPrefix) - self::KEEP_CONTEXT_CHARS);
        }

        return [$string1, $string2];
    }

    private static function findCommonPrefix(string $string1, string $string2): string
    {
        for ($i = 0; $i < strlen($string1); $i++) {
            if (!isset($string2[$i]) || $string1[$i] != $string2[$i]) {
                break;
            }
        }

        return substr($string1, 0, $i);
    }

    /**
     * @return array{string, string}
     */
    private static function removeOverlongCommonSuffix(string $string1, string $string2): array
    {
        $commonSuffix = self::findCommonSuffix($string1, $string2);

        if (strlen($commonSuffix) > self::OVERLONG_THRESHOLD) {
            $string1 = substr($string1, 0, -(strlen($commonSuffix) - self::KEEP_CONTEXT_CHARS)) . '...';
            $string2 = substr($string2, 0, -(strlen($commonSuffix) - self::KEEP_CONTEXT_CHARS)) . '...';
        }

        return [$string1, $string2];
    }

    private static function findCommonSuffix(string $string1, string $string2): string
    {
        if ($string1 === '' || $string2 === '') {
            return '';
        }

        $lastCharIndex1 = strlen($string1) - 1;
        $lastCharIndex2 = strlen($string2) - 1;

        if ($string1[$lastCharIndex1] != $string2[$lastCharIndex2]) {
            return '';
        }

        while (
            $lastCharIndex1 > 0 &&
            $lastCharIndex2 > 0 &&
            $string1[$lastCharIndex1] == $string2[$lastCharIndex2]
        ) {
            $lastCharIndex1--;
            $lastCharIndex2--;
        }

        return substr($string1, $lastCharIndex1 - strlen($string1) + 1);
    }
}
