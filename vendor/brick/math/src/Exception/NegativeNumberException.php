<?php

declare(strict_types=1);

namespace Brick\Math\Exception;

/**
 * Exception thrown when attempting to perform an unsupported operation, such as a square root, on a negative number.
 */
final class NegativeNumberException extends MathException
{
}
