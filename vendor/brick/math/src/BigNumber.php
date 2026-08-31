<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Stringable;

use function array_shift;
use function assert;
use function filter_var;
use function is_float;
use function is_int;
use function is_nan;
use function is_null;
use function ltrim;
use function preg_match;
use function str_contains;
use function str_repeat;
use function strlen;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;
use const FILTER_VALIDATE_INT;
use const PREG_UNMATCHED_AS_NULL;

/**
 * Base class for arbitrary-precision numbers.
 *
 * This class is sealed: it is part of the public API but should not be subclassed in userland.
 * Protected methods may change in any version.
 *
 * @phpstan-sealed BigInteger|BigDecimal|BigRational
 */
abstract readonly class BigNumber implements JsonSerializable, Stringable
{
    /**
     * The regular expression used to parse integer or decimal numbers.
     */
    private const PARSE_REGEXP_NUMERICAL =
        '/^' .
        '(?<sign>[\-\+])?' .
        '(?<integral>[0-9]+)?' .
        '(?<point>\.)?' .
        '(?<fractional>[0-9]+)?' .
        '(?:[eE](?<exponent>[\-\+]?[0-9]+))?' .
        '$/';

    /**
     * The regular expression used to parse rational numbers.
     */
    private const PARSE_REGEXP_RATIONAL =
        '/^' .
        '(?<sign>[\-\+])?' .
        '(?<numerator>[0-9]+)' .
        '\/' .
        '(?<denominator>[0-9]+)' .
        '$/';

    /**
     * Creates a BigNumber of the given value.
     *
     * When of() is called on BigNumber, the concrete return type is dependent on the given value, with the following
     * rules:
     *
     * - BigNumber instances are returned as is
     * - integer numbers are returned as BigInteger
     * - floating point numbers are converted to a string then parsed as such (deprecated, will be removed in 0.15)
     * - strings containing a `/` character are returned as BigRational
     * - strings containing a `.` character or using an exponential notation are returned as BigDecimal
     * - strings containing only digits with an optional leading `+` or `-` sign are returned as BigInteger
     *
     * When of() is called on BigInteger, BigDecimal, or BigRational, the resulting number is converted to an instance
     * of the subclass when possible; otherwise a RoundingNecessaryException exception is thrown.
     *
     * @throws NumberFormatException      If the format of the number is not valid.
     * @throws DivisionByZeroException    If the value represents a rational number with a denominator of zero.
     * @throws RoundingNecessaryException If the value cannot be converted to an instance of the subclass without rounding.
     *
     * @pure
     */
    final public static function of(BigNumber|int|float|string $value): static
    {
        $value = self::_of($value);

        if (static::class === BigNumber::class) {
            assert($value instanceof static);

            return $value;
        }

        return static::from($value);
    }

    /**
     * Creates a BigNumber of the given value, or returns null if the input is null.
     *
     * Behaves like of() for non-null values.
     *
     * @see BigNumber::of()
     *
     * @throws NumberFormatException      If the format of the number is not valid.
     * @throws DivisionByZeroException    If the value represents a rational number with a denominator of zero.
     * @throws RoundingNecessaryException If the value cannot be converted to an instance of the subclass without rounding.
     *
     * @pure
     */
    final public static function ofNullable(BigNumber|int|float|string|null $value): ?static
    {
        if (is_null($value)) {
            return null;
        }

        return static::of($value);
    }

    /**
     * Returns the minimum of the given values.
     *
     * If several values are equal and minimal, the first one is returned.
     * This can affect the concrete return type when calling this method on BigNumber.
     *
     * @param BigNumber|int|float|string ...$values The numbers to compare. All the numbers must be convertible to an
     *                                              instance of the class this method is called on.
     *
     * @throws InvalidArgumentException If no values are given.
     * @throws MathException            If a number is not valid, or is not convertible to an instance of the class
     *                                  this method is called on.
     *
     * @pure
     */
    final public static function min(BigNumber|int|float|string ...$values): static
    {
        $min = null;

        foreach ($values as $value) {
            $value = static::of($value);

            if ($min === null || $value->isLessThan($min)) {
                $min = $value;
            }
        }

        if ($min === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }

        return $min;
    }

    /**
     * Returns the maximum of the given values.
     *
     * If several values are equal and maximal, the first one is returned.
     * This can affect the concrete return type when calling this method on BigNumber.
     *
     * @param BigNumber|int|float|string ...$values The numbers to compare. All the numbers must be convertible to an
     *                                              instance of the class this method is called on.
     *
     * @throws InvalidArgumentException If no values are given.
     * @throws MathException            If a number is not valid, or is not convertible to an instance of the class
     *                                  this method is called on.
     *
     * @pure
     */
    final public static function max(BigNumber|int|float|string ...$values): static
    {
        $max = null;

        foreach ($values as $value) {
            $value = static::of($value);

            if ($max === null || $value->isGreaterThan($max)) {
                $max = $value;
            }
        }

        if ($max === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }

        return $max;
    }

    /**
     * Returns the sum of the given values.
     *
     * When called on BigNumber, sum() accepts any supported type and returns a result whose type is the widest among
     * the given values (BigInteger < BigDecimal < BigRational).
     *
     * When called on BigInteger, BigDecimal, or BigRational, sum() requires that all values can be converted to that
     * specific subclass, and returns a result of the same type.
     *
     * @param BigNumber|int|float|string ...$values The numbers to add. All the numbers must be convertible to an
     *                                              instance of the class this method is called on.
     *
     * @throws InvalidArgumentException If no values are given.
     * @throws MathException            If a number is not valid, or is not convertible to an instance of the class
     *                                  this method is called on.
     *
     * @pure
     */
    final public static function sum(BigNumber|int|float|string ...$values): static
    {
        $first = array_shift($values);

        if ($first === null) {
            throw new InvalidArgumentException(__METHOD__ . '() expects at least one value.');
        }

        $sum = static::of($first);

        foreach ($values as $value) {
            $sum = self::add($sum, static::of($value));
        }

        assert($sum instanceof static);

        return $sum;
    }

    /**
     * Checks if this number is equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isEqualTo(BigNumber|int|float|string $that): bool
    {
        return $this->compareTo($that) === 0;
    }

    /**
     * Checks if this number is strictly less than the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isLessThan(BigNumber|int|float|string $that): bool
    {
        return $this->compareTo($that) < 0;
    }

    /**
     * Checks if this number is less than or equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isLessThanOrEqualTo(BigNumber|int|float|string $that): bool
    {
        return $this->compareTo($that) <= 0;
    }

    /**
     * Checks if this number is strictly greater than the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isGreaterThan(BigNumber|int|float|string $that): bool
    {
        return $this->compareTo($that) > 0;
    }

    /**
     * Checks if this number is greater than or equal to the given one.
     *
     * @throws MathException If the given number is not valid.
     *
     * @pure
     */
    final public function isGreaterThanOrEqualTo(BigNumber|int|float|string $that): bool
    {
        return $this->compareTo($that) >= 0;
    }

    /**
     * Checks if this number equals zero.
     *
     * @pure
     */
    final public function isZero(): bool
    {
        return $this->getSign() === 0;
    }

    /**
     * Checks if this number is strictly negative.
     *
     * @pure
     */
    final public function isNegative(): bool
    {
        return $this->getSign() < 0;
    }

    /**
     * Checks if this number is negative or zero.
     *
     * @pure
     */
    final public function isNegativeOrZero(): bool
    {
        return $this->getSign() <= 0;
    }

    /**
     * Checks if this number is strictly positive.
     *
     * @pure
     */
    final public function isPositive(): bool
    {
        return $this->getSign() > 0;
    }

    /**
     * Checks if this number is positive or zero.
     *
     * @pure
     */
    final public function isPositiveOrZero(): bool
    {
        return $this->getSign() >= 0;
    }

    /**
     * Returns the absolute value of this number.
     *
     * @pure
     */
    final public function abs(): static
    {
        return $this->isNegative() ? $this->negated() : $this;
    }

    /**
     * Returns the negated value of this number.
     *
     * @pure
     */
    abstract public function negated(): static;

    /**
     * Returns the sign of this number.
     *
     * Returns -1 if the number is negative, 0 if zero, 1 if positive.
     *
     * @return -1|0|1
     *
     * @pure
     */
    abstract public function getSign(): int;

    /**
     * Compares this number to the given one.
     *
     * Returns -1 if `$this` is lower than, 0 if equal to, 1 if greater than `$that`.
     *
     * @return -1|0|1
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    abstract public function compareTo(BigNumber|int|float|string $that): int;

    /**
     * Limits (clamps) this number between the given minimum and maximum values.
     *
     * If the number is lower than $min, returns $min.
     * If the number is greater than $max, returns $max.
     * Otherwise, returns this number unchanged.
     *
     * @param BigNumber|int|float|string $min The minimum. Must be convertible to an instance of the class this method is called on.
     * @param BigNumber|int|float|string $max The maximum. Must be convertible to an instance of the class this method is called on.
     *
     * @throws MathException            If min/max are not convertible to an instance of the class this method is called on.
     * @throws InvalidArgumentException If min is greater than max.
     *
     * @pure
     */
    final public function clamp(BigNumber|int|float|string $min, BigNumber|int|float|string $max): static
    {
        $min = static::of($min);
        $max = static::of($max);

        if ($min->isGreaterThan($max)) {
            throw new InvalidArgumentException('Minimum value must be less than or equal to maximum value.');
        }

        if ($this->isLessThan($min)) {
            return $min;
        }

        if ($this->isGreaterThan($max)) {
            return $max;
        }

        return $this;
    }

    /**
     * Converts this number to a BigInteger.
     *
     * @throws RoundingNecessaryException If this number cannot be converted to a BigInteger without rounding.
     *
     * @pure
     */
    abstract public function toBigInteger(): BigInteger;

    /**
     * Converts this number to a BigDecimal.
     *
     * @throws RoundingNecessaryException If this number cannot be converted to a BigDecimal without rounding.
     *
     * @pure
     */
    abstract public function toBigDecimal(): BigDecimal;

    /**
     * Converts this number to a BigRational.
     *
     * @pure
     */
    abstract public function toBigRational(): BigRational;

    /**
     * Converts this number to a BigDecimal with the given scale, using rounding if necessary.
     *
     * @param int          $scale        The scale of the resulting `BigDecimal`. Must be non-negative.
     * @param RoundingMode $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used, and this number cannot be converted to
     *                                    the given scale without rounding.
     *
     * @pure
     */
    abstract public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal;

    /**
     * Returns the exact value of this number as a native integer.
     *
     * If this number cannot be converted to a native integer without losing precision, an exception is thrown.
     * Note that the acceptable range for an integer depends on the platform and differs for 32-bit and 64-bit.
     *
     * @throws MathException If this number cannot be exactly converted to a native integer.
     *
     * @pure
     */
    abstract public function toInt(): int;

    /**
     * Returns an approximation of this number as a floating-point value.
     *
     * Note that this method can discard information as the precision of a floating-point value
     * is inherently limited.
     *
     * If the number is greater than the largest representable floating point number, positive infinity is returned.
     * If the number is less than the smallest representable floating point number, negative infinity is returned.
     * This method never returns NaN.
     *
     * @pure
     */
    abstract public function toFloat(): float;

    /**
     * Returns a string representation of this number.
     *
     * The output of this method can be parsed by the `of()` factory method; this will yield an object equal to this
     * one, but possibly of a different type if instantiated through `BigNumber::of()`.
     *
     * @pure
     */
    abstract public function toString(): string;

    #[Override]
    final public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * @pure
     */
    final public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Overridden by subclasses to convert a BigNumber to an instance of the subclass.
     *
     * @throws RoundingNecessaryException If the value cannot be converted.
     *
     * @pure
     */
    abstract protected static function from(BigNumber $number): static;

    /**
     * Proxy method to access BigInteger's protected constructor from sibling classes.
     *
     * @internal
     *
     * @pure
     */
    final protected function newBigInteger(string $value): BigInteger
    {
        return new BigInteger($value);
    }

    /**
     * Proxy method to access BigDecimal's protected constructor from sibling classes.
     *
     * @internal
     *
     * @pure
     */
    final protected function newBigDecimal(string $value, int $scale = 0): BigDecimal
    {
        return new BigDecimal($value, $scale);
    }

    /**
     * Proxy method to access BigRational's protected constructor from sibling classes.
     *
     * @internal
     *
     * @pure
     */
    final protected function newBigRational(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator): BigRational
    {
        return new BigRational($numerator, $denominator, $checkDenominator);
    }

    /**
     * @throws NumberFormatException   If the format of the number is not valid.
     * @throws DivisionByZeroException If the value represents a rational number with a denominator of zero.
     *
     * @pure
     */
    private static function _of(BigNumber|int|float|string $value): BigNumber
    {
        if ($value instanceof BigNumber) {
            return $value;
        }

        if (is_int($value)) {
            return new BigInteger((string) $value);
        }

        if (is_float($value)) {
            // @phpstan-ignore-next-line
            trigger_error(
                'Passing floats to BigNumber::of() and arithmetic methods is deprecated and will be removed in 0.15. ' .
                'Cast the float to string explicitly to preserve the previous behaviour.',
                E_USER_DEPRECATED,
            );

            if (is_nan($value)) {
                $value = 'NAN';
            } else {
                $value = (string) $value;
            }
        }

        if (str_contains($value, '/')) {
            // Rational number
            if (preg_match(self::PARSE_REGEXP_RATIONAL, $value, $matches, PREG_UNMATCHED_AS_NULL) !== 1) {
                throw NumberFormatException::invalidFormat($value);
            }

            $sign = $matches['sign'];
            $numerator = $matches['numerator'];
            $denominator = $matches['denominator'];

            $numerator = self::cleanUp($sign, $numerator);
            $denominator = self::cleanUp(null, $denominator);

            if ($denominator === '0') {
                throw DivisionByZeroException::denominatorMustNotBeZero();
            }

            return new BigRational(
                new BigInteger($numerator),
                new BigInteger($denominator),
                false,
            );
        } else {
            // Integer or decimal number
            if (preg_match(self::PARSE_REGEXP_NUMERICAL, $value, $matches, PREG_UNMATCHED_AS_NULL) !== 1) {
                throw NumberFormatException::invalidFormat($value);
            }

            $sign = $matches['sign'];
            $point = $matches['point'];
            $integral = $matches['integral'];
            $fractional = $matches['fractional'];
            $exponent = $matches['exponent'];

            if ($integral === null && $fractional === null) {
                throw NumberFormatException::invalidFormat($value);
            }

            if ($integral === null) {
                $integral = '0';
            }

            if ($point !== null || $exponent !== null) {
                $fractional ??= '';

                if ($exponent !== null) {
                    if ($exponent[0] === '-') {
                        $exponent = ltrim(substr($exponent, 1), '0') ?: '0';
                        $exponent = filter_var($exponent, FILTER_VALIDATE_INT);
                        if ($exponent !== false) {
                            $exponent = -$exponent;
                        }
                    } else {
                        if ($exponent[0] === '+') {
                            $exponent = substr($exponent, 1);
                        }
                        $exponent = ltrim($exponent, '0') ?: '0';
                        $exponent = filter_var($exponent, FILTER_VALIDATE_INT);
                    }
                } else {
                    $exponent = 0;
                }

                if ($exponent === false) {
                    throw new NumberFormatException('Exponent too large.');
                }

                $unscaledValue = self::cleanUp($sign, $integral . $fractional);

                $scale = strlen($fractional) - $exponent;

                if ($scale < 0) {
                    if ($unscaledValue !== '0') {
                        $unscaledValue .= str_repeat('0', -$scale);
                    }
                    $scale = 0;
                }

                return new BigDecimal($unscaledValue, $scale);
            }

            $integral = self::cleanUp($sign, $integral);

            return new BigInteger($integral);
        }
    }

    /**
     * Removes optional leading zeros and applies sign.
     *
     * @param string|null $sign   The sign, '+' or '-', optional. Null is allowed for convenience and treated as '+'.
     * @param string      $number The number, validated as a string of digits.
     *
     * @pure
     */
    private static function cleanUp(string|null $sign, string $number): string
    {
        $number = ltrim($number, '0');

        if ($number === '') {
            return '0';
        }

        return $sign === '-' ? '-' . $number : $number;
    }

    /**
     * Adds two BigNumber instances in the correct order to avoid a RoundingNecessaryException.
     *
     * @pure
     */
    private static function add(BigNumber $a, BigNumber $b): BigNumber
    {
        if ($a instanceof BigRational) {
            return $a->plus($b);
        }

        if ($b instanceof BigRational) {
            return $b->plus($a);
        }

        if ($a instanceof BigDecimal) {
            return $a->plus($b);
        }

        if ($b instanceof BigDecimal) {
            return $b->plus($a);
        }

        return $a->plus($b);
    }
}
