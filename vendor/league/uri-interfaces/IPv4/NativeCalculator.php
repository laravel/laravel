<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\IPv4;

use function floor;
use function intval;

final class NativeCalculator implements Calculator
{
    public function baseConvert(mixed $value, int $base): int
    {
        return intval((string) $value, $base);
    }

    public function pow(mixed $value, int $exponent)
    {
        return $value ** $exponent;
    }

    public function compare(mixed $value1, mixed $value2): int
    {
        return $value1 <=> $value2;
    }

    public function multiply(mixed $value1, mixed $value2): int
    {
        return $value1 * $value2;
    }

    public function div(mixed $value, mixed $base): int
    {
        return (int) floor($value / $base);
    }

    public function mod(mixed $value, mixed $base): int
    {
        return $value % $base;
    }

    public function add(mixed $value1, mixed $value2): int
    {
        return $value1 + $value2;
    }

    public function sub(mixed $value1, mixed $value2): int
    {
        return $value1 - $value2;
    }
}
