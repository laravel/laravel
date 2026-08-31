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

use function count;
use function sprintf;
use Exception;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use PHPUnit\Util\Test;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Parameters implements ParametersRule
{
    /**
     * @var list<Constraint>
     */
    private array $parameters           = [];
    private ?BaseInvocation $invocation = null;
    private null|bool|ExpectationFailedException $parameterVerificationResult;

    /**
     * @param array<mixed> $parameters
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct(array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (!$parameter instanceof Constraint) {
                $parameter = new IsEqual(
                    $parameter,
                );
            }

            $this->parameters[] = $parameter;
        }
    }

    /**
     * @throws Exception
     */
    public function apply(BaseInvocation $invocation): void
    {
        $this->invocation                  = $invocation;
        $this->parameterVerificationResult = null;

        try {
            $this->parameterVerificationResult = $this->doVerify();
        } catch (ExpectationFailedException $e) {
            $this->parameterVerificationResult = $e;

            throw $this->parameterVerificationResult;
        }
    }

    /**
     * Checks if the invocation $invocation matches the current rules. If it
     * does the rule will get the invoked() method called which should check
     * if an expectation is met.
     *
     * @throws ExpectationFailedException
     */
    public function verify(): void
    {
        $this->doVerify();
    }

    /**
     * @throws ExpectationFailedException
     */
    private function doVerify(): bool
    {
        if (isset($this->parameterVerificationResult)) {
            return $this->guardAgainstDuplicateEvaluationOfParameterConstraints();
        }

        if ($this->invocation === null) {
            throw new ExpectationFailedException('Doubled method does not exist.');
        }

        if (count($this->invocation->parameters()) < count($this->parameters)) {
            $message = 'Parameter count for invocation %s is too low.';

            // The user called `->with($this->anything())`, but may have meant
            // `->withAnyParameters()`.
            //
            // @see https://github.com/sebastianbergmann/phpunit-mock-objects/issues/199
            if (count($this->parameters) === 1 &&
                $this->parameters[0]::class === IsAnything::class) {
                $message .= "\nTo allow 0 or more parameters with any value, omit ->with() or use ->withAnyParameters() instead.";
            }

            $this->incrementAssertionCount();

            throw new ExpectationFailedException(
                sprintf($message, $this->invocation->toString()),
            );
        }

        foreach ($this->parameters as $i => $parameter) {
            if ($parameter instanceof Callback && $parameter->isVariadic()) {
                $other = $this->invocation->parameters();
            } else {
                $other = $this->invocation->parameters()[$i];
            }

            $this->incrementAssertionCount();

            $parameter->evaluate(
                $other,
                sprintf(
                    'Parameter %s for invocation %s does not match expected value.',
                    $i,
                    $this->invocation->toString(),
                ),
            );
        }

        return true;
    }

    /**
     * @throws ExpectationFailedException
     */
    private function guardAgainstDuplicateEvaluationOfParameterConstraints(): bool
    {
        if ($this->parameterVerificationResult instanceof ExpectationFailedException) {
            throw $this->parameterVerificationResult;
        }

        return (bool) $this->parameterVerificationResult;
    }

    private function incrementAssertionCount(): void
    {
        Test::currentTestCase()->addToAssertionCount(1);
    }
}
