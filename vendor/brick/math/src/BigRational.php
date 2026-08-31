<?php

declare(strict_types=1);

namespace Brick\Math;

use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use InvalidArgumentException;
use LogicException;
use Override;

use function is_finite;
use function max;
use function min;
use function strlen;
use function substr;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * An arbitrarily large rational number.
 *
 * This class is immutable.
 *
 * Fractions are automatically simplified to lowest terms. For example, `2/4` becomes `1/2`.
 * The denominator is always strictly positive; the sign is carried by the numerator.
 */
final readonly class BigRational extends BigNumber
{
    /**
     * The numerator.
     */
    private BigInteger $numerator;

    /**
     * The denominator. Always strictly positive.
     */
    private BigInteger $denominator;

    /**
     * Protected constructor. Use a factory method to obtain an instance.
     *
     * @param BigInteger $numerator        The numerator.
     * @param BigInteger $denominator      The denominator.
     * @param bool       $checkDenominator Whether to check the denominator for negative and zero.
     *
     * @throws DivisionByZeroException If the denominator is zero.
     *
     * @pure
     */
    protected function __construct(BigInteger $numerator, BigInteger $denominator, bool $checkDenominator)
    {
        if ($checkDenominator) {
            if ($denominator->isZero()) {
                throw DivisionByZeroException::denominatorMustNotBeZero();
            }

            if ($denominator->isNegative()) {
                $numerator = $numerator->negated();
                $denominator = $denominator->negated();
            }
        }

        $this->numerator = $numerator;
        $this->denominator = $denominator;
    }

    /**
     * Creates a BigRational out of a numerator and a denominator.
     *
     * If the denominator is negative, the signs of both the numerator and the denominator
     * will be inverted to ensure that the denominator is always positive.
     *
     * @deprecated Use ofFraction() instead.
     *
     * @param BigNumber|int|float|string $numerator   The numerator. Must be convertible to a BigInteger.
     * @param BigNumber|int|float|string $denominator The denominator. Must be convertible to a BigInteger.
     *
     * @throws NumberFormatException      If an argument does not represent a valid number.
     * @throws RoundingNecessaryException If an argument represents a non-integer number.
     * @throws DivisionByZeroException    If the denominator is zero.
     */
    public static function nd(
        BigNumber|int|float|string $numerator,
        BigNumber|int|float|string $denominator,
    ): BigRational {
        trigger_error(
            'The BigRational::nd() method is deprecated, use BigRational::ofFraction() instead.',
            E_USER_DEPRECATED,
        );

        return self::ofFraction($numerator, $denominator);
    }

    /**
     * Creates a BigRational out of a numerator and a denominator.
     *
     * If the denominator is negative, the signs of both the numerator and the denominator
     * will be inverted to ensure that the denominator is always positive.
     *
     * @param BigNumber|int|float|string $numerator   The numerator. Must be convertible to a BigInteger.
     * @param BigNumber|int|float|string $denominator The denominator. Must be convertible to a BigInteger.
     *
     * @throws MathException           If an argument is not valid, or is not convertible to a BigInteger.
     * @throws DivisionByZeroException If the denominator is zero.
     *
     * @pure
     */
    public static function ofFraction(
        BigNumber|int|float|string $numerator,
        BigNumber|int|float|string $denominator,
    ): BigRational {
        $numerator = BigInteger::of($numerator);
        $denominator = BigInteger::of($denominator);

        return new BigRational($numerator, $denominator, true);
    }

    /**
     * Returns a BigRational representing zero.
     *
     * @pure
     */
    public static function zero(): BigRational
    {
        /** @var BigRational|null $zero */
        static $zero;

        if ($zero === null) {
            $zero = new BigRational(BigInteger::zero(), BigInteger::one(), false);
        }

        return $zero;
    }

    /**
     * Returns a BigRational representing one.
     *
     * @pure
     */
    public static function one(): BigRational
    {
        /** @var BigRational|null $one */
        static $one;

        if ($one === null) {
            $one = new BigRational(BigInteger::one(), BigInteger::one(), false);
        }

        return $one;
    }

    /**
     * Returns a BigRational representing ten.
     *
     * @pure
     */
    public static function ten(): BigRational
    {
        /** @var BigRational|null $ten */
        static $ten;

        if ($ten === null) {
            $ten = new BigRational(BigInteger::ten(), BigInteger::one(), false);
        }

        return $ten;
    }

    /**
     * @pure
     */
    public function getNumerator(): BigInteger
    {
        return $this->numerator;
    }

    /**
     * @pure
     */
    public function getDenominator(): BigInteger
    {
        return $this->denominator;
    }

    /**
     * Returns the quotient of the division of the numerator by the denominator.
     *
     * @deprecated Will be removed in 0.15. Use getIntegralPart() instead.
     */
    public function quotient(): BigInteger
    {
        trigger_error(
            'BigRational::quotient() is deprecated and will be removed in 0.15. Use getIntegralPart() instead.',
            E_USER_DEPRECATED,
        );

        return $this->numerator->quotient($this->denominator);
    }

    /**
     * Returns the remainder of the division of the numerator by the denominator.
     *
     * @deprecated Will be removed in 0.15. Use `$number->getNumerator()->remainder($number->getDenominator())` instead.
     */
    public function remainder(): BigInteger
    {
        trigger_error(
            'BigRational::remainder() is deprecated and will be removed in 0.15. Use `$number->getNumerator()->remainder($number->getDenominator())` instead.',
            E_USER_DEPRECATED,
        );

        return $this->numerator->remainder($this->denominator);
    }

    /**
     * Returns the quotient and remainder of the division of the numerator by the denominator.
     *
     * @deprecated Will be removed in 0.15. Use `$number->getNumerator()->quotientAndRemainder($number->getDenominator())` instead.
     *
     * @return array{BigInteger, BigInteger}
     */
    public function quotientAndRemainder(): array
    {
        trigger_error(
            'BigRational::quotientAndRemainder() is deprecated and will be removed in 0.15. Use `$number->getNumerator()->quotientAndRemainder($number->getDenominator())` instead.',
            E_USER_DEPRECATED,
        );

        return $this->numerator->quotientAndRemainder($this->denominator);
    }

    /**
     * Returns the integral part of this rational number.
     *
     * Examples:
     *
     * - `7/3` returns `2` (since 7/3 = 2 + 1/3)
     * - `-7/3` returns `-2` (since -7/3 = -2 + (-1/3))
     *
     * The following identity holds: `$r->isEqualTo($r->getFractionalPart()->plus($r->getIntegralPart()))`.
     *
     * @pure
     */
    public function getIntegralPart(): BigInteger
    {
        return $this->numerator->quotient($this->denominator);
    }

    /**
     * Returns the fractional part of this rational number.
     *
     * Examples:
     *
     * - `7/3` returns `1/3` (since 7/3 = 2 + 1/3)
     * - `-7/3` returns `-1/3` (since -7/3 = -2 + (-1/3))
     *
     * The following identity holds: `$r->isEqualTo($r->getFractionalPart()->plus($r->getIntegralPart()))`.
     *
     * @pure
     */
    public function getFractionalPart(): BigRational
    {
        return new BigRational($this->numerator->remainder($this->denominator), $this->denominator, false);
    }

    /**
     * Returns the sum of this number and the given one.
     *
     * @param BigNumber|int|float|string $that The number to add.
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    public function plus(BigNumber|int|float|string $that): BigRational
    {
        $that = BigRational::of($that);

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->plus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false);
    }

    /**
     * Returns the difference of this number and the given one.
     *
     * @param BigNumber|int|float|string $that The number to subtract.
     *
     * @throws MathException If the number is not valid.
     *
     * @pure
     */
    public function minus(BigNumber|int|float|string $that): BigRational
    {
        $that = BigRational::of($that);

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $numerator = $numerator->minus($that->numerator->multipliedBy($this->denominator));
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false);
    }

    /**
     * Returns the product of this number and the given one.
     *
     * @param BigNumber|int|float|string $that The multiplier.
     *
     * @throws MathException If the multiplier is not valid.
     *
     * @pure
     */
    public function multipliedBy(BigNumber|int|float|string $that): BigRational
    {
        $that = BigRational::of($that);

        $numerator = $this->numerator->multipliedBy($that->numerator);
        $denominator = $this->denominator->multipliedBy($that->denominator);

        return new BigRational($numerator, $denominator, false);
    }

    /**
     * Returns the result of the division of this number by the given one.
     *
     * @param BigNumber|int|float|string $that The divisor.
     *
     * @throws MathException           If the divisor is not valid.
     * @throws DivisionByZeroException If the divisor is zero.
     *
     * @pure
     */
    public function dividedBy(BigNumber|int|float|string $that): BigRational
    {
        $that = BigRational::of($that);

        if ($that->isZero()) {
            throw DivisionByZeroException::divisionByZero();
        }

        $numerator = $this->numerator->multipliedBy($that->denominator);
        $denominator = $this->denominator->multipliedBy($that->numerator);

        return new BigRational($numerator, $denominator, true);
    }

    /**
     * Returns this number exponentiated to the given value.
     *
     * @throws InvalidArgumentException If the exponent is not in the range 0 to 1,000,000.
     *
     * @pure
     */
    public function power(int $exponent): BigRational
    {
        if ($exponent === 0) {
            return BigRational::one();
        }

        if ($exponent === 1) {
            return $this;
        }

        return new BigRational(
            $this->numerator->power($exponent),
            $this->denominator->power($exponent),
            false,
        );
    }

    /**
     * Returns the reciprocal of this BigRational.
     *
     * The reciprocal has the numerator and denominator swapped.
     *
     * @throws DivisionByZeroException If the numerator is zero.
     *
     * @pure
     */
    public function reciprocal(): BigRational
    {
        return new BigRational($this->denominator, $this->numerator, true);
    }

    #[Override]
    public function negated(): static
    {
        return new BigRational($this->numerator->negated(), $this->denominator, false);
    }

    /**
     * Returns the simplified value of this BigRational.
     *
     * @pure
     */
    public function simplified(): BigRational
    {
        $gcd = $this->numerator->gcd($this->denominator);

        $numerator = $this->numerator->quotient($gcd);
        $denominator = $this->denominator->quotient($gcd);

        return new BigRational($numerator, $denominator, false);
    }

    #[Override]
    public function compareTo(BigNumber|int|float|string $that): int
    {
        $that = BigRational::of($that);

        if ($this->denominator->isEqualTo($that->denominator)) {
            return $this->numerator->compareTo($that->numerator);
        }

        return $this->numerator
            ->multipliedBy($that->denominator)
            ->compareTo($that->numerator->multipliedBy($this->denominator));
    }

    #[Override]
    public function getSign(): int
    {
        return $this->numerator->getSign();
    }

    #[Override]
    public function toBigInteger(): BigInteger
    {
        $simplified = $this->simplified();

        if (! $simplified->denominator->isEqualTo(1)) {
            throw new RoundingNecessaryException('This rational number cannot be represented as an integer value without rounding.');
        }

        return $simplified->numerator;
    }

    #[Override]
    public function toBigDecimal(): BigDecimal
    {
        return $this->numerator->toBigDecimal()->dividedByExact($this->denominator);
    }

    #[Override]
    public function toBigRational(): BigRational
    {
        return $this;
    }

    #[Override]
    public function toScale(int $scale, RoundingMode $roundingMode = RoundingMode::Unnecessary): BigDecimal
    {
        return $this->numerator->toBigDecimal()->dividedBy($this->denominator, $scale, $roundingMode);
    }

    #[Override]
    public function toInt(): int
    {
        return $this->toBigInteger()->toInt();
    }

    #[Override]
    public function toFloat(): float
    {
        $simplified = $this->simplified();
        $numeratorFloat = $simplified->numerator->toFloat();
        $denominatorFloat = $simplified->denominator->toFloat();

        if (is_finite($numeratorFloat) && is_finite($denominatorFloat)) {
            return $numeratorFloat / $denominatorFloat;
        }

        // At least one side overflows to INF; use a decimal approximation instead.
        // We need ~17 significant digits for double precision (we use 20 for some margin). Since $scale controls
        // decimal places (not significant digits), we subtract the estimated order of magnitude so that large results
        // use fewer decimal places and small results use more (to look past leading zeros). Clamped to [0, 350] as
        // doubles range from e-324 to e308 (350 ≈ 324 + 20 significant digits + margin).
        $magnitude = strlen($simplified->numerator->abs()->toString()) - strlen($simplified->denominator->toString());
        $scale = min(350, max(0, 20 - $magnitude));

        return $simplified->numerator
            ->toBigDecimal()
            ->dividedBy($simplified->denominator, $scale, RoundingMode::HalfEven)
            ->toFloat();
    }

    #[Override]
    public function toString(): string
    {
        $numerator = $this->numerator->toString();
        $denominator = $this->denominator->toString();

        if ($denominator === '1') {
            return $numerator;
        }

        return $numerator . '/' . $denominator;
    }

    /**
     * Returns the decimal representation of this rational number, with repeating decimals in parentheses.
     *
     * WARNING: This method is unbounded.
     *          The length of the repeating decimal period can be as large as `denominator - 1`.
     *          For fractions with large denominators, this method can use excessive memory and CPU time.
     *          For example, `1/100019` has a repeating period of 100,018 digits.
     *
     * Examples:
     *
     * - `10/3` returns `3.(3)`
     * - `171/70` returns `2.4(428571)`
     * - `1/2` returns `0.5`
     *
     * @pure
     */
    public function toRepeatingDecimalString(): string
    {
        if ($this->numerator->isZero()) {
            return '0';
        }

        $sign = $this->numerator->isNegative() ? '-' : '';
        $numerator = $this->numerator->abs();
        $denominator = $this->denominator;

        $integral = $numerator->quotient($denominator);
        $remainder = $numerator->remainder($denominator);

        $integralString = $integral->toString();

        if ($remainder->isZero()) {
            return $sign . $integralString;
        }

        $digits = '';
        $remainderPositions = [];
        $index = 0;

        while (! $remainder->isZero()) {
            $remainderString = $remainder->toString();

            if (isset($remainderPositions[$remainderString])) {
                $repeatIndex = $remainderPositions[$remainderString];
                $nonRepeating = substr($digits, 0, $repeatIndex);
                $repeating = substr($digits, $repeatIndex);

                return $sign . $integralString . '.' . $nonRepeating . '(' . $repeating . ')';
            }

            $remainderPositions[$remainderString] = $index;
            $remainder = $remainder->multipliedBy(10);

            $digits .= $remainder->quotient($denominator)->toString();
            $remainder = $remainder->remainder($denominator);
            $index++;
        }

        return $sign . $integralString . '.' . $digits;
    }

    /**
     * This method is required for serializing the object and SHOULD NOT be accessed directly.
     *
     * @internal
     *
     * @return array{numerator: BigInteger, denominator: BigInteger}
     */
    public function __serialize(): array
    {
        return ['numerator' => $this->numerator, 'denominator' => $this->denominator];
    }

    /**
     * This method is only here to allow unserializing the object and cannot be accessed directly.
     *
     * @internal
     *
     * @param array{numerator: BigInteger, denominator: BigInteger} $data
     *
     * @throws LogicException
     */
    public function __unserialize(array $data): void
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->numerator)) {
            throw new LogicException('__unserialize() is an internal function, it must not be called directly.');
        }

        /** @phpstan-ignore deadCode.unreachable */
        $this->numerator = $data['numerator'];
        $this->denominator = $data['denominator'];
    }

    #[Override]
    protected static function from(BigNumber $number): static
    {
        return $number->toBigRational();
    }
}
