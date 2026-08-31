<?php declare(strict_types=1);
/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Diff;

use function array_fill;
use function array_merge;
use function array_reverse;
use function array_slice;
use function count;
use function in_array;
use function max;

final class MemoryEfficientLongestCommonSubsequenceCalculator implements LongestCommonSubsequenceCalculator
{
    /**
     * @inheritDoc
     */
    public function calculate(array $from, array $to): array
    {
        $cFrom = count($from);
        $cTo   = count($to);

        if ($cFrom === 0) {
            return [];
        }

        if ($cFrom === 1) {
            if (in_array($from[0], $to, true)) {
                return [$from[0]];
            }

            return [];
        }

        $i         = (int) ($cFrom / 2);
        $fromStart = array_slice($from, 0, $i);
        $fromEnd   = array_slice($from, $i);
        $llB       = $this->length($fromStart, $to);
        $llE       = $this->length(array_reverse($fromEnd), array_reverse($to));
        $jMax      = 0;
        $max       = 0;

        for ($j = 0; $j <= $cTo; $j++) {
            $m = $llB[$j] + $llE[$cTo - $j];

            if ($m >= $max) {
                $max  = $m;
                $jMax = $j;
            }
        }

        $toStart = array_slice($to, 0, $jMax);
        $toEnd   = array_slice($to, $jMax);

        return array_merge(
            $this->calculate($fromStart, $toStart),
            $this->calculate($fromEnd, $toEnd),
        );
    }

    private function length(array $from, array $to): array
    {
        $current = array_fill(0, count($to) + 1, 0);
        $cFrom   = count($from);
        $cTo     = count($to);

        for ($i = 0; $i < $cFrom; $i++) {
            $prev = $current;

            for ($j = 0; $j < $cTo; $j++) {
                if ($from[$i] === $to[$j]) {
                    $current[$j + 1] = $prev[$j] + 1;
                } else {
                    /**
                     * @noinspection PhpConditionCanBeReplacedWithMinMaxCallInspection
                     *
                     * We do not use max() here to avoid the function call overhead
                     */
                    if ($current[$j] > $prev[$j + 1]) {
                        $current[$j + 1] = $current[$j];
                    } else {
                        $current[$j + 1] = $prev[$j + 1];
                    }
                }
            }
        }

        return $current;
    }
}
