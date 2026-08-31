<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\Internal\Calculator;
use Brick\Math\Internal\CalculatorRegistry;
use InvalidArgumentException;
use LogicException;
use Override;

use function func_num_args;
use function in_array;
use function intdiv;
use function max;
use function rtrim;
use function sprintf;
use function str_pad;
use function str_repeat;
use function strlen;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;
use const STR_PAD_LEFT;

/**
 * An arbitrarily large decimal number.
 *
 * This class is immutable.
 *
 * The scale of the number is the number of digits after the decimal point. It is always positive or zero.
 */
final readonly class BigDecimal extends BigNumber
{
    /**
     * The unscaled value of this decimal number.
     *
     * This is a string of digits with an optional leading minus sign.
     * No leading zero must be present.
     * No leading minus sign must be present if the value is 0.
     */
    private string $value;

    /**
     * The scale (number of digits after the decimal point) of this decimal number.
     *
     * This must be zero or more.
     */
    private int $scale;

    /**
     * Protected constructor. Use a factory method to obtain an instance.
     *
     * @param string $value The unscaled value, validated.
     * @param int    $scale The scale, validated.
     *
     * @pure
     */
    protected function __construct(string $value, int $scale = 0)
    {
        $this->value = $value;
        $this->scale = $scale;
    }

    /**
     * Creates a BigDecimal from an unscaled value and a scale.
     *
     * Example: `(12345, 3)` will result in the BigDecimal `12.345`.
     *
     * A negative scale is normalized to zero by appending zeros to the unscaled value.
     *
     * Example: `(12345, -3)` will result in the BigDecimal `12345000`.
     *
     * @param BigNumber|int|float|string $value The unscaled value. Must be convertible to a BigInteger.
     * @param int                        $scale The scale of the number. If negative, the scale will be set to zero
     *                                          and the unscaled value will be adjusted accordingly.
     *
     * @throws MathException If the value is not valid, or is not convertible to a BigInteger.
     *
     * @pure
     */
    public static function ofUnscaledValue(BigNumber|int|float|string $value, int $scale = 0): BigDecimal
    {
        $value = BigInteger::of($value)->toString();

        if ($scale < 0) {
            if ($value !== '0') {
                $value .= str_repeat('0', -$scale);
            }
            $scale = 0;
        }

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns a BigDecimal representing zero, with a scale of zero.
     *
     * @pure
     */
    public static function zero(): BigDecimal
    {
        /** @var BigDecimal|null $zero */
        static $zero;

        if ($zero === null) {
            $zero = new BigDecimal('0');
        }

        return $zero;
    }

    /**
     * Returns a BigDecimal representing one, with a scale of zero.
     *
     * @pure
     */
    public static function one(): BigDecimal
    {
        /** @var BigDecimal|null $one */
        static $one;

        if ($one === null) {
            $one = new BigDecimal('1');
        }

        return $one;
    }

    /**
     * Returns a BigDecimal representing ten, with a scale of zero.
     *
     * @pure
     */
    public static function ten(): BigDecimal
    {
        /** @var BigDecimal|null $ten */
        static $ten;

        if ($ten === null) {
            $ten = new BigDecimal('10');
        }

        return $ten;
    }

    /**
     * Returns the sum of this number and the given one.
     *
     * The result has a scale of `max($this->scale, $that->scale)`.
     *
     * @param BigNumber|int|float|string $that The number to add. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the number is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function plus(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->value === '0' && $that->scale <= $this->scale) {
            return $this;
        }

        if ($this->value === '0' && $this->scale <= $that->scale) {
            return $that;
        }

        [$a, $b] = $this->scaleValues($this, $that);

        $value = CalculatorRegistry::get()->add($a, $b);
        $scale = max($this->scale, $that->scale);

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the difference of this number and the given one.
     *
     * The result has a scale of `max($this->scale, $that->scale)`.
     *
     * @param BigNumber|int|float|string $that The number to subtract. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the number is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function minus(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->value === '0' && $that->scale <= $this->scale) {
            return $this;
        }

        [$a, $b] = $this->scaleValues($this, $that);

        $value = CalculatorRegistry::get()->sub($a, $b);
        $scale = max($this->scale, $that->scale);

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the product of this number and the given one.
     *
     * The result has a scale of `$this->scale + $that->scale`.
     *
     * @param BigNumber|int|float|string $that The multiplier. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the multiplier is not valid, or is not convertible to a BigDecimal.
     *
     * @pure
     */
    public function multipliedBy(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->value === '1' && $that->scale === 0) {
            return $this;
        }

        if ($this->value === '1' && $this->scale === 0) {
            return $that;
        }

        $value = CalculatorRegistry::get()->mul($this->value, $that->value);
        $scale = $this->scale + $that->scale;

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns the result of the division of this number by the given one, at the given scale.
     *
     * @param BigNumber|int|float|string $that         The divisor. Must be convertible to a BigDecimal.
     * @param int|null                   $scale        The desired scale. Omitting this parameter is deprecated; it will be required in 0.15.
     * @param RoundingMode               $roundingMode An optional rounding mode, defaults to Unnecessary.
     *
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws MathException              If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException    If the divisor is zero.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used and the result cannot be represented
     *                                    exactly at the given scale.
     *
     * @pure
     */
    public function dividedBy(BigNumber|int|float|string $that, ?int $scale = null, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        if ($scale === null) {
            // @phpstan-ignore-next-line
            trigger_error(
                'Not passing a $scale to BigDecimal::dividedBy() is deprecated. ' .
                'Use $a->dividedBy($b, $a->getScale(), $roundingMode) to retain current behavior.',
                E_USER_DEPRECATED,
            );
            $scale = $this->scale;
        } elseif ($scale < 0) {
            throw new InvalidArgumentException('Scale must not be negative.');
        }

        if ($that->value === '1' && $that->scale === 0 && $scale === $this->scale) {
            return $this;
        }

        $p = $this->valueWithMinScale($that->scale + $scale);
        $q = $that->valueWithMinScale($this->scale - $scale);

        $result = CalculatorRegistry::get()->divRound($p, $q, $roundingMode);

        return new BigDecimal($result, $scale);
    }

    /**
     * Returns the exact result of the division of this number by the given one.
     *
     * The scale of the result is automatically calculated to fit all the fraction digits.
     *
     * @deprecated Will be removed in 0.15. Use dividedByExact() instead.
     *
     * @param BigNumber|int|float|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException If the divisor is not a valid number, is not convertible to a BigDecimal, is zero,
     *                       or the result yields an infinite number of digits.
     */
    public function exactlyDividedBy(BigNumber|int|float|string $that): BigDecimal
    {
        trigger_error(
            'BigDecimal::exactlyDividedBy() is deprecated and will be removed in 0.15. Use dividedByExact() instead.',
            E_USER_DEPRECATED,
        );

        return $this->dividedByExact($that);
    }

    /**
     * Returns the exact result of the division of this number by the given one.
     *
     * The scale of the result is automatically calculated to fit all the fraction digits.
     *
     * @param BigNumber|int|float|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException              If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException    If the divisor is zero.
     * @throws RoundingNecessaryException If the result yields an infinite number of digits.
     *
     * @pure
     */
    public function dividedByExact(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->value === '0') {
            throw DivisionByZeroException::divisionByZero();
        }

        [, $b] = $this->scaleValues($this, $that);

        $d = rtrim($b, '0');
        $scale = strlen($b) - strlen($d);

        $calculator = CalculatorRegistry::get();

        foreach ([5, 2] as $prime) {
            for (; ;) {
                $lastDigit = (int) $d[-1];

                if ($lastDigit % $prime !== 0) {
                    break;
                }

                $d = $calculator->divQ($d, (string) $prime);
                $scale++;
            }
        }

        return $this->dividedBy($that, $scale)->strippedOfTrailingZeros();
    }

    /**
     * Returns this number exponentiated to the given value.
     *
     * The result has a scale of `$this->scale * $exponent`.
     *
     * @throws InvalidArgumentException If the exponent is not in the range 0 to 1,000,000.
     *
     * @pure
     */
    public function power(int $exponent): BigDecimal
    {
        if ($exponent === 0) {
            return BigDecimal::one();
        }

        if ($exponent === 1) {
            return $this;
        }

        if ($exponent < 0 || $exponent > Calculator::MAX_POWER) {
            throw new InvalidArgumentException(sprintf(
                'The exponent %d is not in the range 0 to %d.',
                $exponent,
                Calculator::MAX_POWER,
            ));
        }

        return new BigDecimal(CalculatorRegistry::get()->pow($this->value, $exponent), $this->scale * $exponent);
    }

    /**
     * Returns the quotient of the division of this number by the given one.
     *
     * The quotient has a scale of `0`.
     *
     * Examples:
     *
     * - `7.5` quotient `3` returns `2`
     * - `7.5` quotient `-3` returns `-2`
     * - `-7.5` quotient `3` returns `-2`
     * - `-7.5` quotient `-3` returns `2`
     *
     * @param BigNumber|int|float|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function quotient(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        $quotient = CalculatorRegistry::get()->divQ($p, $q);

        return new BigDecimal($quotient, 0);
    }

    /**
     * Returns the remainder of the division of this number by the given one.
     *
     * The remainder has a scale of `max($this->scale, $that->scale)`.
     * The remainder, when non-zero, has the same sign as the dividend.
     *
     * Examples:
     *
     * - `7.5` remainder `3` returns `1.5`
     * - `7.5` remainder `-3` returns `1.5`
     * - `-7.5` remainder `3` returns `-1.5`
     * - `-7.5` remainder `-3` returns `-1.5`
     *
     * @param BigNumber|int|float|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function remainder(BigNumber|int|float|string $that): BigDecimal
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        $remainder = CalculatorRegistry::get()->divR($p, $q);

        $scale = max($this->scale, $that->scale);

        return new BigDecimal($remainder, $scale);
    }

    /**
     * Returns the quotient and remainder of the division of this number by the given one.
     *
     * The quotient has a scale of `0`, and the remainder has a scale of `max($this->scale, $that->scale)`.
     *
     * Examples:
     *
     * - `7.5` quotientAndRemainder `3` returns [`2`, `1.5`]
     * - `7.5` quotientAndRemainder `-3` returns [`-2`, `1.5`]
     * - `-7.5` quotientAndRemainder `3` returns [`-2`, `-1.5`]
     * - `-7.5` quotientAndRemainder `-3` returns [`2`, `-1.5`]
     *
     * @param BigNumber|int|float|string $that The divisor. Must be convertible to a BigDecimal.
     *
     * @return array{BigDecimal, BigDecimal} An array containing the quotient and the remainder.
     *
     * @throws MathException           If the divisor is not valid, or is not convertible to a BigDecimal.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function quotientAndRemainder(BigNumber|int|float|string $that): array
    {
        $that = BigDecimal::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $p = $this->valueWithMinScale($that->scale);
        $q = $that->valueWithMinScale($this->scale);

        [$quotient, $remainder] = CalculatorRegistry::get()->divQR($p, $q);

        $scale = max($this->scale, $that->scale);

        $quotient = new BigDecimal($quotient, 0);
        $remainder = new BigDecimal($remainder, $scale);

        return [$quotient, $remainder];
    }

    /**
     * Returns the square root of this number, rounded to the given scale according to the given rounding mode.
     *
     * @param int          $scale        The target scale. Must be non-negative.
     * @param RoundingMode $roundingMode The rounding mode to use, defaults to Down.
     *                                   ⚠️ WARNING: the default rounding mode was kept as Down for backward
     *                                   compatibility, but will change to Unnecessary in version 0.15. Pass a rounding
     *                                   mode explicitly to avoid this upcoming breaking change.
     *
     * @throws InvalidArgumentException   If the scale is negative.
     * @throws NegativeNumberException    If this number is negative.
     * @throws RoundingNecessaryException If RoundingMode::Unnecessary is used and the result cannot be represented
     *                                    exactly at the given scale.
     *
     * @pure
     */
    public function sqrt(int $scale, RoundingMode $roundingMode = RoundingMode::Down): BigDecimal
    {
        if (func_num_args() === 1) {
            // @phpstan-ignore-next-line
            trigger_error(
                'The default rounding mode of BigDecimal::sqrt() will change from Down to Unnecessary in version 0.15. ' .
                'Pass a rounding mode explicitly to avoid this breaking change.',
                E_USER_DEPRECATED,
            );
        }

        if ($scale < 0) {
            throw new InvalidArgumentException('Scale must not be negative.');
        }

        if ($this->value === '0') {
            return new BigDecimal('0', $scale);
        }

        if ($this->value[0] === '-') {
            throw new NegativeNumberException('Cannot calculate the square root of a negative number.');
        }

        $value = $this->value;
        $inputScale = $this->scale;

        if ($inputScale % 2 !== 0) {
            $value .= '0';
            $inputScale++;
        }

        $calculator = CalculatorRegistry::get();

        // Keep one extra digit for rounding.
        $intermediateScale = max($scale, intdiv($inputScale, 2)) + 1;
        $value .= str_repeat('0', 2 * $intermediateScale - $inputScale);

        $sqrt = $calculator->sqrt($value);
        $isExact = $calculator->mul($sqrt, $sqrt) === $value;

        if (! $isExact) {
            if ($roundingMode === RoundingMode::Unnecessary) {
                throw RoundingNecessaryException::roundingNecessary();
            }

            // Non-perfect-square sqrt is irrational, so the true value is strictly above this sqrt floor.
            // Add one at the intermediate scale to guarantee Up/Ceiling round up at the target scale.
            if (in_array($roundingMode, [RoundingMode::Up, RoundingMode::Ceiling], true)) {
                $sqrt = $calculator->add($sqrt, '1');
            }

            // Irrational sqrt cannot land exactly on a midpoint; treat tie-to-down modes as HalfUp.
            elseif (in_array($roundingMode, [RoundingMode::HalfDown, RoundingMode::HalfEven, RoundingMode::HalfFloor], true)) {
                $roundingMode = RoundingMode::HalfUp;
            }
        }

        return (new BigDecimal($sqrt, $intermediateScale))->toScale($scale, $roundingMode);
    }

    /**
     * Returns a copy of this BigDecimal with the decimal point moved to the left by the given number of places.
     *
     * @pure
     */
    public function withPointMovedLeft(int $n): BigDecimal
    {
        if ($n === 0) {
            return $this;
        }

        if ($n < 0) {
            return $this->withPointMovedRight(-$n);
        }

        return new BigDecimal($this->value, $this->scale + $n);
    }

    /**
     * Returns a copy of this BigDecimal with the decimal point moved to the right by the given number of places.
     *
     * @pure
     */
    public function withPointMovedRight(int $n): BigDecimal
    {
        if ($n === 0) {
            return $this;
        }

        if ($n < 0) {
            return $this->withPointMovedLeft(-$n);
        }

        $value = $this->value;
        $scale = $this->scale - $n;

        if ($scale < 0) {
            if ($value !== '0') {
                $value .= str_repeat('0', -$scale);
            }
            $scale = 0;
        }

        return new BigDecimal($value, $scale);
    }

    /**
     * Returns a copy of this BigDecimal with any trailing zeros removed from the fractional part.
     *
     * @deprecated Use strippedOfTrailingZeros() instead.
     */
    public function stripTrailingZeros(): BigDecimal
    {
        trigger_error(
            'BigDecimal::stripTrailingZeros() is deprecated, use strippedOfTrailingZeros() instead.',
            E_USER_DEPRECATED,
        );

        return $this->strippedOfTrailingZeros();
    }

    /**
     * Returns a copy of this BigDecimal with any trailing zeros removed from the fractional part.
     *
     * @pure
     */
    public function strippedOfTrailingZeros(): BigDecimal
    {
        if ($this->scale === 0) {
            return $this;
        }

        $trimmedValue = rtrim($this->value, '0');

        if ($trimmedValue === '') {
            return BigDecimal::zero();
        }

        $trimmableZeros = strlen($this->value) - strlen($trimmedValue);

        if ($trimmableZeros === 0) {
            return $this;
        }

        if ($trimmableZeros > $this->scale) {
            $trimmableZeros = $this->scale;
        }

        $value = substr($this->value, 0, -$trimmableZeros);
        $scale = $this->scale - $trimmableZeros;

        return new BigDecimal($value, $scale);
    }

    #[Override]
    public function negated(): static
    {
        return new BigDecimal(CalculatorRegistry::get()->neg($this->value), $this->scale);
    }

    #[Override]
    public function compareTo(BigNumber|int|float|string $that): int
    {
        $that = BigNumber::of($that);

        if ($that instanceof BigInteger) {
            $that = $that->toBigDecimal();
        }

        if ($that instanceof BigDecimal) {
            [$a, $b] = $this->scaleValues($this, $that);

            return CalculatorRegistry::get()->cmp($a, $b);
        }

        return -$that->compareTo($this);
    }

    #[Override]
    public function getSign(): int
    {
        return ($this->value === '0') ? 0 : (($this->value[0] === '-') ? -1 : 1);
    }

    /**
     * @pure
     */
    public function getUnscaledValue(): BigInteger
    {
        return self::newBigInteger($this->value);
    }

    /**
     * @pure
     */
    public function getScale(): int
    {
        return $this->scale;
    }

    /**
     * Returns the number of significant digits in the number.
     *
     * This is the number of digits to both sides of the decimal point, stripped of leading zeros.
     * The sign has no impact on the result.
     *
     * Examples:
     *   0 => 0
     *   0.0 => 0
     *   123 => 3
     *   123.456 => 6
     *   0.00123 => 3
     *   0.0012300 => 5
     *
     * @pure
     */
    public function getPrecision(): int
    {
        $value = $this->value;

        if ($value === '0') {
            return 0;
        }

        $length = strlen($value);

        return ($value[0] === '-') ? $length - 1 : $length;
    }

    /**
     * Returns a string representing the integral part of this decimal number.
     *
     * Example: `-123.456` => `-123`.
     *
     * @deprecated Will be removed in 0.15 and re-introduced as returning BigInteger in 0.16.
     */
    public function getIntegralPart(): string
    {
        trigger_error(
            'BigDecimal::getIntegralPart() is deprecated and will be removed in 0.15. It will be re-introduced as returning BigInteger in 0.16.',
            E_USER_DEPRECATED,
        );

        if ($this->scale === 0) {
            return $this->value;
        }

        $value = $this->getUnscaledValueWithLeadingZeros();

        return substr($value, 0, -$this->scale);
    }

    /**
     * Returns a string representing the fractional part of this decimal number.
     *
     * If the scale is zero, an empty string is returned.
     *
     * Examples: `-123.456` => '456', `123` => ''.
     *
     * @deprecated Will be removed in 0.15 and re-introduced as returning BigDecimal with a different meaning in 0.16.
     */
    public function getFractionalPart(): string
    {
        trigger_error(
            'BigDecimal::getFractionalPart() is deprecated and will be removed in 0.15. It will be re-introduced as returning BigDecimal with a different meaning in 0.16.',
            E_USER_DEPRECATED,
        );

        if ($this->scale === 0) {
            return '';
        }

        $value = $this->getUnscaledValueWithLeadingZeros();

        return substr($value, -$this->scale);
    }

    /**
     * Returns whether this decimal number has a non-zero fractional part.
     *
     * @pure
     */
    public function hasNonZeroFractionalPart(): bool
    {
        if ($this->scale === 0) {
            return false;
        }

        $value = $this->getUnscaledValueWithLeadingZeros();

        return substr($value, -$this->scale) !== str_repeat('0', $this->scale);
    }

    #[Override]
    public function toBigInteger(): BigInteger
    {
        $zeroScaleDecimal = $this->scale === 0 ? $this : $this->dividedBy(1, 0);

        return self::newBigInteger($zeroScaleDecimal->value);
    }

    #[Override]
    public function toBigDecimal(): BigDecimal
    {
        return $this;
    }

    #[Override]
    public function toBigRational(): BigRational
    {
        $numerator = self::newBigInteger($this->value);
        $denominator = self::newBigInteger('1' . str_repeat('0', $this->scale));

        return self::newBigRational($numerator, $denominator, false);
    }

    #[Override]
    public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        if ($scale === $this->scale) {
            return $this;
        }

        return $this->dividedBy(BigDecimal::one(), $scale, $roundingMode);
    }

    #[Override]
    public function toInt(): int
    {
        return $this->toBigInteger()->toInt();
    }

    #[Override]
    public function toFloat(): float
    {
        return (float) $this->toString();
    }

    /**
     * @return numeric-string
     */
    #[Override]
    public function toString(): string
    {
        if ($this->scale === 0) {
            /** @var numeric-string */
            return $this->value;
        }

        $value = $this->getUnscaledValueWithLeadingZeros();

        /** @phpstan-ignore return.type */
        return substr($value, 0, -$this->scale) . '.' . substr($value, -$this->scale);
    }

    /**
     * This method is required for serializing the object and SHOULD NOT be accessed directly.
     *
     * @internal
     *
     * @return array{value: string, scale: int}
     */
    public function __serialize(): array
    {
        return ['value' => $this->value, 'scale' => $this->scale];
    }

    /**
     * This method is only here to allow unserializing the object and cannot be accessed directly.
     *
     * @internal
     *
     * @param array{value: string, scale: int} $data
     *
     * @throws LogicException
     */
    public function __unserialize(array $data): void
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->value)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }

        /** @phpstan-ignore deadCode.unreachable */
        $this->value = $data['value'];
        $this->scale = $data['scale'];
    }

    #[Override]
    protected static function from(BigNumber $number): static
    {
        return $number->toBigDecimal();
    }

    /**
     * Puts the internal values of the given decimal numbers on the same scale.
     *
     * @return array{string, string} The scaled integer values of $x and $y.
     *
     * @pure
     */
    private function scaleValues(BigDecimal $x, BigDecimal $y): array
    {
        $a = $x->value;
        $b = $y->value;

        if ($b !== '0' && $x->scale > $y->scale) {
            $b .= str_repeat('0', $x->scale - $y->scale);
        } elseif ($a !== '0' && $x->scale < $y->scale) {
            $a .= str_repeat('0', $y->scale - $x->scale);
        }

        return [$a, $b];
    }

    /**
     * @pure
     */
    private function valueWithMinScale(int $scale): string
    {
        $value = $this->value;

        if ($this->value !== '0' && $scale > $this->scale) {
            $value .= str_repeat('0', $scale - $this->scale);
        }

        return $value;
    }

    /**
     * Adds leading zeros if necessary to the unscaled value to represent the full decimal number.
     *
     * @pure
     */
    private function getUnscaledValueWithLeadingZeros(): string
    {
        $value = $this->value;
        $targetLength = $this->scale + 1;
        $negative = ($value[0] === '-');
        $length = strlen($value);

        if ($negative) {
            $length--;
        }

        if ($length >= $targetLength) {
            return $this->value;
        }

        if ($negative) {
            $value = substr($value, 1);
        }

        $value = str_pad($value, $targetLength, '0', STR_PAD_LEFT);

        if ($negative) {
            $value = '-' . $value;
        }

        return $value;
    }
}
