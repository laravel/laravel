<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

use Brick\Math\BigInteger;

use function sprintf;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Exception thrown when an integer overflow occurs.
 */
final class IntegerOverflowException extends MathException
{
    /**
     * @pure
     */
    public static function toIntOverflow(BigInteger $value): IntegerOverflowException
    {
        $message = '%s is out of range %d to %d and cannot be represented as an integer.';

        return new self(sprintf($message, $value->toString(), PHP_INT_MIN, PHP_INT_MAX));
    }
}
