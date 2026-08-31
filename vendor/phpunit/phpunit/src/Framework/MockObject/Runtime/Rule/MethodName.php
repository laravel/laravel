<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Rule;

use function is_string;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use PHPUnit\Framework\MockObject\MethodNameConstraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MethodName
{
    private Constraint $constraint;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(Constraint|string $constraint)
    {
        if (is_string($constraint)) {
            $constraint = new MethodNameConstraint($constraint);
        }

        $this->constraint = $constraint;
    }

    public function toString(): string
    {
        return 'method name ' . $this->constraint->toString();
    }

    /**
     * @throws ExpectationFailedException
     */
    public function matches(BaseInvocation $invocation): bool
    {
        return $this->matchesName($invocation->methodName());
    }

    /**
     * @throws ExpectationFailedException
     */
    public function matchesName(string $methodName): bool
    {
        return (bool) $this->constraint->evaluate($methodName, '', true);
    }
}
