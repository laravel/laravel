<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\Constraint;

use function sprintf;
use PHPUnit\Util\Filter;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Exception extends Constraint
{
    private readonly string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return sprintf(
            'exception of type "%s"',
            $this->className,
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     */
    protected function matches(mixed $other): bool
    {
        return $other instanceof $this->className;
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @throws \PHPUnit\Framework\Exception
     */
    protected function failureDescription(mixed $other): string
    {
        if ($other === null) {
            return sprintf(
                'exception of type "%s" is thrown',
                $this->className,
            );
        }

        $message = '';

        if ($other instanceof Throwable) {
            $message = '. Message was: "' . $other->getMessage() . '" at'
                . "\n" . Filter::stackTraceFromThrowableAsString($other);
        }

        return sprintf(
            'exception of type "%s" matches expected exception "%s"%s',
            $other::class,
            $this->className,
            $message,
        );
    }
}
