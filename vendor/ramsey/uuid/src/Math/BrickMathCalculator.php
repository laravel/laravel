<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid\Math;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\Exception\MathException;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Type\Decimal;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Type\NumberInterface;

/**
 * A calculator using the brick/math library for arbitrary-precision arithmetic
 *
 * @immutable
 */
final class BrickMathCalculator implements CalculatorInterface
{
    public function add(NumberInterface $augend, NumberInterface ...$addends): NumberInterface
    {
        $sum = BigInteger::of($augend->toString());

        foreach ($addends as $addend) {
            $sum = $sum->plus($addend->toString());
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new IntegerObject((string) $sum);
    }

    public function subtract(NumberInterface $minuend, NumberInterface ...$subtrahends): NumberInterface
    {
        $difference = BigInteger::of($minuend->toString());

        foreach ($subtrahends as $subtrahend) {
            $difference = $difference->minus($subtrahend->toString());
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new IntegerObject((string) $difference);
    }

    public function multiply(NumberInterface $multiplicand, NumberInterface ...$multipliers): NumberInterface
    {
        $product = BigInteger::of($multiplicand->toString());

        foreach ($multipliers as $multiplier) {
            $product = $product->multipliedBy($multiplier->toString());
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new IntegerObject((string) $product);
    }

    public function divide(
        int $roundingMode,
        int $scale,
        NumberInterface $dividend,
        NumberInterface ...$divisors,
    ): NumberInterface {
        /** @phpstan-ignore possiblyImpure.methodCall */
        $brickRounding = BrickMathRoundingMode::resolve($roundingMode);

        $quotient = BigDecimal::of($dividend->toString());

        foreach ($divisors as $divisor) {
            $quotient = $quotient->dividedBy($divisor->toString(), $scale, $brickRounding);
        }

        if ($scale === 0) {
            /** @phpstan-ignore possiblyImpure.new */
            return new IntegerObject((string) $quotient->toBigInteger());
        }

        /** @phpstan-ignore possiblyImpure.new */
        return new Decimal((string) $quotient);
    }

    public function fromBase(string $value, int $base): IntegerObject
    {
        try {
            /** @phpstan-ignore possiblyImpure.new */
            return new IntegerObject((string) BigInteger::fromBase($value, $base));
        } catch (MathException | \InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(), /** @phpstan-ignore possiblyImpure.methodCall */
                (int) $exception->getCode(), /** @phpstan-ignore possiblyImpure.methodCall */
                $exception
            );
        }
    }

    public function toBase(IntegerObject $value, int $base): string
    {
        try {
            return BigInteger::of($value->toString())->toBase($base);
        } catch (MathException | \InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getMessage(), /** @phpstan-ignore possiblyImpure.methodCall */
                (int) $exception->getCode(), /** @phpstan-ignore possiblyImpure.methodCall */
                $exception
            );
        }
    }

    public function toHexadecimal(IntegerObject $value): Hexadecimal
    {
        /** @phpstan-ignore possiblyImpure.new */
        return new Hexadecimal($this->toBase($value, 16));
    }

    public function toInteger(Hexadecimal $value): IntegerObject
    {
        return $this->fromBase($value->toString(), 16);
    }
}
