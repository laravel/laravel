<?php

declare(strict_types=1);

namespace Brick\Math\Internal;

use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

use function chr;
use function ltrim;
use function ord;
use function str_repeat;
use function strlen;
use function strpos;
use function strrev;
use function strtolower;
use function substr;

/**
 * Performs basic operations on arbitrary size integers.
 *
 * Unless otherwise specified, all parameters must be validated as non-empty strings of digits,
 * without leading zero, and with an optional leading minus sign if the number is not zero.
 *
 * Any other parameter format will lead to undefined behaviour.
 * All methods must return strings respecting this format, unless specified otherwise.
 *
 * @internal
 */
abstract readonly class Calculator
{
    /**
     * The maximum exponent value allowed for the pow() method.
     */
    public const MAX_POWER = 1_000_000;

    /**
     * The alphabet for converting from and to base 2 to 36, lowercase.
     */
    public const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * Returns the absolute value of a number.
     *
     * @pure
     */
    final public function abs(string $n): string
    {
        return ($n[0] === '-') ? substr($n, 1) : $n;
    }

    /**
     * Negates a number.
     *
     * @pure
     */
    final public function neg(string $n): string
    {
        if ($n === '0') {
            return '0';
        }

        if ($n[0] === '-') {
            return substr($n, 1);
        }

        return '-' . $n;
    }

    /**
     * Compares two numbers.
     *
     * Returns -1 if the first number is less than, 0 if equal to, 1 if greater than the second number.
     *
     * @return -1|0|1
     *
     * @pure
     */
    final public function cmp(string $a, string $b): int
    {
        [$aNeg, $bNeg, $aDig, $bDig] = $this->init($a, $b);

        if ($aNeg && ! $bNeg) {
            return -1;
        }

        if ($bNeg && ! $aNeg) {
            return 1;
        }

        $aLen = strlen($aDig);
        $bLen = strlen($bDig);

        if ($aLen < $bLen) {
            $result = -1;
        } elseif ($aLen > $bLen) {
            $result = 1;
        } else {
            $result = $aDig <=> $bDig;
        }

        return $aNeg ? -$result : $result;
    }

    /**
     * Adds two numbers.
     *
     * @pure
     */
    abstract public function add(string $a, string $b): string;

    /**
     * Subtracts two numbers.
     *
     * @pure
     */
    abstract public function sub(string $a, string $b): string;

    /**
     * Multiplies two numbers.
     *
     * @pure
     */
    abstract public function mul(string $a, string $b): string;

    /**
     * Returns the quotient of the division of two numbers.
     *
     * @param string $a The dividend.
     * @param string $b The divisor, must not be zero.
     *
     * @return string The quotient.
     *
     * @pure
     */
    abstract public function divQ(string $a, string $b): string;

    /**
     * Returns the remainder of the division of two numbers.
     *
     * @param string $a The dividend.
     * @param string $b The divisor, must not be zero.
     *
     * @return string The remainder.
     *
     * @pure
     */
    abstract public function divR(string $a, string $b): string;

    /**
     * Returns the quotient and remainder of the division of two numbers.
     *
     * @param string $a The dividend.
     * @param string $b The divisor, must not be zero.
     *
     * @return array{string, string} An array containing the quotient and remainder.
     *
     * @pure
     */
    abstract public function divQR(string $a, string $b): array;

    /**
     * Exponentiates a number.
     *
     * @param string $a The base number.
     * @param int    $e The exponent, validated as an integer between 0 and MAX_POWER.
     *
     * @return string The power.
     *
     * @pure
     */
    abstract public function pow(string $a, int $e): string;

    /**
     * @param string $b The modulus; must not be zero.
     *
     * @pure
     */
    public function mod(string $a, string $b): string
    {
        return $this->divR($this->add($this->divR($a, $b), $b), $b);
    }

    /**
     * Returns the modular multiplicative inverse of $x modulo $m.
     *
     * If $x has no multiplicative inverse mod m, this method must return null.
     *
     * This method can be overridden by the concrete implementation if the underlying library has built-in support.
     *
     * @param string $m The modulus; must not be negative or zero.
     *
     * @pure
     */
    public function modInverse(string $x, string $m): ?string
    {
        if ($m === '1') {
            return '0';
        }

        $modVal = $x;

        if ($x[0] === '-' || ($this->cmp($this->abs($x), $m) >= 0)) {
            $modVal = $this->mod($x, $m);
        }

        [$g, $x] = $this->gcdExtended($modVal, $m);

        if ($g !== '1') {
            return null;
        }

        return $this->mod($this->add($this->mod($x, $m), $m), $m);
    }

    /**
     * Raises a number into power with modulo.
     *
     * @param string $base The base number.
     * @param string $exp  The exponent; must be positive or zero.
     * @param string $mod  The modulus; must be strictly positive.
     *
     * @pure
     */
    abstract public function modPow(string $base, string $exp, string $mod): string;

    /**
     * Returns the greatest common divisor of the two numbers.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for GCD calculations.
     *
     * @return string The GCD, always positive, or zero if both arguments are zero.
     *
     * @pure
     */
    public function gcd(string $a, string $b): string
    {
        if ($a === '0') {
            return $this->abs($b);
        }

        if ($b === '0') {
            return $this->abs($a);
        }

        return $this->gcd($b, $this->divR($a, $b));
    }

    /**
     * Returns the least common multiple of the two numbers.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for LCM calculations.
     *
     * @return string The LCM, always positive, or zero if at least one argument is zero.
     *
     * @pure
     */
    public function lcm(string $a, string $b): string
    {
        if ($a === '0' || $b === '0') {
            return '0';
        }

        return $this->divQ($this->abs($this->mul($a, $b)), $this->gcd($a, $b));
    }

    /**
     * Returns the square root of the given number, rounded down.
     *
     * The result is the largest x such that x² ≤ n.
     * The input MUST NOT be negative.
     *
     * @pure
     */
    abstract public function sqrt(string $n): string;

    /**
     * Converts a number from an arbitrary base.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for base conversion.
     *
     * @param string $number The number, positive or zero, non-empty, case-insensitively validated for the given base.
     * @param int    $base   The base of the number, validated from 2 to 36.
     *
     * @return string The converted number, following the Calculator conventions.
     *
     * @pure
     */
    public function fromBase(string $number, int $base): string
    {
        return $this->fromArbitraryBase(strtolower($number), self::ALPHABET, $base);
    }

    /**
     * Converts a number to an arbitrary base.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for base conversion.
     *
     * @param string $number The number to convert, following the Calculator conventions.
     * @param int    $base   The base to convert to, validated from 2 to 36.
     *
     * @return string The converted number, lowercase.
     *
     * @pure
     */
    public function toBase(string $number, int $base): string
    {
        $negative = ($number[0] === '-');

        if ($negative) {
            $number = substr($number, 1);
        }

        $number = $this->toArbitraryBase($number, self::ALPHABET, $base);

        if ($negative) {
            return '-' . $number;
        }

        return $number;
    }

    /**
     * Converts a non-negative number in an arbitrary base using a custom alphabet, to base 10.
     *
     * @param string $number   The number to convert, validated as a non-empty string,
     *                         containing only chars in the given alphabet/base.
     * @param string $alphabet The alphabet that contains every digit, validated as 2 chars minimum.
     * @param int    $base     The base of the number, validated from 2 to alphabet length.
     *
     * @return string The number in base 10, following the Calculator conventions.
     *
     * @pure
     */
    final public function fromArbitraryBase(string $number, string $alphabet, int $base): string
    {
        // remove leading "zeros"
        $number = ltrim($number, $alphabet[0]);

        if ($number === '') {
            return '0';
        }

        // optimize for "one"
        if ($number === $alphabet[1]) {
            return '1';
        }

        $result = '0';
        $power = '1';

        $base = (string) $base;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $index = strpos($alphabet, $number[$i]);

            if ($index !== 0) {
                $result = $this->add(
                    $result,
                    ($index === 1) ? $power : $this->mul($power, (string) $index),
                );
            }

            if ($i !== 0) {
                $power = $this->mul($power, $base);
            }
        }

        return $result;
    }

    /**
     * Converts a non-negative number to an arbitrary base using a custom alphabet.
     *
     * @param string $number   The number to convert, positive or zero, following the Calculator conventions.
     * @param string $alphabet The alphabet that contains every digit, validated as 2 chars minimum.
     * @param int    $base     The base to convert to, validated from 2 to alphabet length.
     *
     * @return string The converted number in the given alphabet.
     *
     * @pure
     */
    final public function toArbitraryBase(string $number, string $alphabet, int $base): string
    {
        if ($number === '0') {
            return $alphabet[0];
        }

        $base = (string) $base;
        $result = '';

        while ($number !== '0') {
            [$number, $remainder] = $this->divQR($number, $base);
            $remainder = (int) $remainder;

            $result .= $alphabet[$remainder];
        }

        return strrev($result);
    }

    /**
     * Performs a rounded division.
     *
     * Rounding is performed when the remainder of the division is not zero.
     *
     * @param string       $a            The dividend.
     * @param string       $b            The divisor, must not be zero.
     * @param RoundingMode $roundingMode The rounding mode.
     *
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is provided but rounding is necessary.
     *
     * @pure
     */
    final public function divRound(string $a, string $b, RoundingMode $roundingMode): string
    {
        [$quotient, $remainder] = $this->divQR($a, $b);

        $hasDiscardedFraction = ($remainder !== '0');
        $isPositiveOrZero = ($a[0] === '-') === ($b[0] === '-');

        $discardedFractionSign = function () use ($remainder, $b): int {
            $r = $this->abs($this->mul($remainder, '2'));
            $b = $this->abs($b);

            return $this->cmp($r, $b);
        };

        $increment = false;

        switch ($roundingMode) {
            case RoundingMode::Unnecessary:
                if ($hasDiscardedFraction) {
                    throw RoundingNecessaryException::roundingNecessary();
                }

                break;

            case RoundingMode::Up:
                $increment = $hasDiscardedFraction;

                break;

            case RoundingMode::Down:
                break;

            case RoundingMode::Ceiling:
                $increment = $hasDiscardedFraction && $isPositiveOrZero;

                break;

            case RoundingMode::Floor:
                $increment = $hasDiscardedFraction && ! $isPositiveOrZero;

                break;

            case RoundingMode::HalfUp:
                $increment = $discardedFractionSign() >= 0;

                break;

            case RoundingMode::HalfDown:
                $increment = $discardedFractionSign() > 0;

                break;

            case RoundingMode::HalfCeiling:
                $increment = $isPositiveOrZero ? $discardedFractionSign() >= 0 : $discardedFractionSign() > 0;

                break;

            case RoundingMode::HalfFloor:
                $increment = $isPositiveOrZero ? $discardedFractionSign() > 0 : $discardedFractionSign() >= 0;

                break;

            case RoundingMode::HalfEven:
                $lastDigit = (int) $quotient[-1];
                $lastDigitIsEven = ($lastDigit % 2 === 0);
                $increment = $lastDigitIsEven ? $discardedFractionSign() > 0 : $discardedFractionSign() >= 0;

                break;
        }

        if ($increment) {
            return $this->add($quotient, $isPositiveOrZero ? '1' : '-1');
        }

        return $quotient;
    }

    /**
     * Calculates bitwise AND of two numbers.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for bitwise operations.
     *
     * @pure
     */
    public function and(string $a, string $b): string
    {
        return $this->bitwise('and', $a, $b);
    }

    /**
     * Calculates bitwise OR of two numbers.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for bitwise operations.
     *
     * @pure
     */
    public function or(string $a, string $b): string
    {
        return $this->bitwise('or', $a, $b);
    }

    /**
     * Calculates bitwise XOR of two numbers.
     *
     * This method can be overridden by the concrete implementation if the underlying library
     * has built-in support for bitwise operations.
     *
     * @pure
     */
    public function xor(string $a, string $b): string
    {
        return $this->bitwise('xor', $a, $b);
    }

    /**
     * Extracts the sign & digits of the operands.
     *
     * @return array{bool, bool, string, string} Whether $a and $b are negative, followed by their digits.
     *
     * @pure
     */
    final protected function init(string $a, string $b): array
    {
        return [
            $aNeg = ($a[0] === '-'),
            $bNeg = ($b[0] === '-'),

            $aNeg ? substr($a, 1) : $a,
            $bNeg ? substr($b, 1) : $b,
        ];
    }

    /**
     * @return array{string, string, string} GCD, X, Y
     *
     * @pure
     */
    private function gcdExtended(string $a, string $b): array
    {
        if ($a === '0') {
            return [$b, '0', '1'];
        }

        [$gcd, $x1, $y1] = $this->gcdExtended($this->mod($b, $a), $a);

        $x = $this->sub($y1, $this->mul($this->divQ($b, $a), $x1));
        $y = $x1;

        return [$gcd, $x, $y];
    }

    /**
     * Performs a bitwise operation on a decimal number.
     *
     * @param 'and'|'or'|'xor' $operator The operator to use.
     * @param string           $a        The left operand.
     * @param string           $b        The right operand.
     *
     * @pure
     */
    private function bitwise(string $operator, string $a, string $b): string
    {
        [$aNeg, $bNeg, $aDig, $bDig] = $this->init($a, $b);

        $aBin = $this->toBinary($aDig);
        $bBin = $this->toBinary($bDig);

        $aLen = strlen($aBin);
        $bLen = strlen($bBin);

        if ($aLen > $bLen) {
            $bBin = str_repeat("\x00", $aLen - $bLen) . $bBin;
        } elseif ($bLen > $aLen) {
            $aBin = str_repeat("\x00", $bLen - $aLen) . $aBin;
        }

        if ($aNeg) {
            $aBin = $this->twosComplement($aBin);
        }
        if ($bNeg) {
            $bBin = $this->twosComplement($bBin);
        }

        $value = match ($operator) {
            'and' => $aBin & $bBin,
            'or' => $aBin | $bBin,
            'xor' => $aBin ^ $bBin,
        };

        $negative = match ($operator) {
            'and' => $aNeg and $bNeg,
            'or' => $aNeg or $bNeg,
            'xor' => $aNeg xor $bNeg,
        };

        if ($negative) {
            $value = $this->twosComplement($value);
        }

        $result = $this->toDecimal($value);

        return $negative ? $this->neg($result) : $result;
    }

    /**
     * @param string $number A positive, binary number.
     *
     * @pure
     */
    private function twosComplement(string $number): string
    {
        $xor = str_repeat("\xff", strlen($number));

        $number ^= $xor;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $byte = ord($number[$i]);

            if (++$byte !== 256) {
                $number[$i] = chr($byte);

                break;
            }

            $number[$i] = "\x00";

            if ($i === 0) {
                $number = "\x01" . $number;
            }
        }

        return $number;
    }

    /**
     * Converts a decimal number to a binary string.
     *
     * @param string $number The number to convert, positive or zero, only digits.
     *
     * @pure
     */
    private function toBinary(string $number): string
    {
        $result = '';

        while ($number !== '0') {
            [$number, $remainder] = $this->divQR($number, '256');
            $result .= chr((int) $remainder);
        }

        return strrev($result);
    }

    /**
     * Returns the positive decimal representation of a binary number.
     *
     * @param string $bytes The bytes representing the number.
     *
     * @pure
     */
    private function toDecimal(string $bytes): string
    {
        $result = '0';
        $power = '1';

        for ($i = strlen($bytes) - 1; $i >= 0; $i--) {
            $index = ord($bytes[$i]);

            if ($index !== 0) {
                $result = $this->add(
                    $result,
                    ($index === 1) ? $power : $this->mul($power, (string) $index),
                );
            }

            if ($i !== 0) {
                $power = $this->mul($power, '256');
            }
        }

        return $result;
    }
}
