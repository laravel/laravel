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

use GMP;

use function gmp_add;
use function gmp_cmp;
use function gmp_div_q;
use function gmp_init;
use function gmp_mod;
use function gmp_mul;
use function gmp_pow;
use function gmp_sub;

use const GMP_ROUND_MINUSINF;

final class GMPCalculator implements Calculator
{
    public function baseConvert(mixed $value, int $base): GMP
    {
        return gmp_init($value, $base);
    }

    public function pow(mixed $value, int $exponent): GMP
    {
        return gmp_pow($value, $exponent);
    }

    public function compare(mixed $value1, mixed $value2): int
    {
        return gmp_cmp($value1, $value2);
    }

    public function multiply(mixed $value1, mixed $value2): GMP
    {
        return gmp_mul($value1, $value2);
    }

    public function div(mixed $value, mixed $base): GMP
    {
        return gmp_div_q($value, $base, GMP_ROUND_MINUSINF);
    }

    public function mod(mixed $value, mixed $base): GMP
    {
        return gmp_mod($value, $base);
    }

    public function add(mixed $value1, mixed $value2): GMP
    {
        return gmp_add($value1, $value2);
    }

    public function sub(mixed $value1, mixed $value2): GMP
    {
        return gmp_sub($value1, $value2);
    }
}
