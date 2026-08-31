<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Builder;

use function array_flip;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_pop;
use function assert;
use function count;
use function is_string;
use function range;
use function strtolower;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\ConfigurableMethod;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\InvocationHandler;
use PHPUnit\Framework\MockObject\Matcher;
use PHPUnit\Framework\MockObject\MatcherAlreadyRegisteredException;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameNotConfiguredException;
use PHPUnit\Framework\MockObject\MethodParametersAlreadyConfiguredException;
use PHPUnit\Framework\MockObject\Rule;
use PHPUnit\Framework\MockObject\Runtime\PropertyHook;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap;
use PHPUnit\Framework\MockObject\Stub\Stub;
use Throwable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class InvocationMocker implements InvocationStubber, MethodNameMatch
{
    private readonly InvocationHandler $invocationHandler;
    private readonly Matcher $matcher;

    /**
     * @var list<ConfigurableMethod>
     */
    private readonly array $configurableMethods;

    /**
     * @var ?array<string, int>
     */
    private ?array $configurableMethodNames = null;

    public function __construct(InvocationHandler $handler, Matcher $matcher, ConfigurableMethod ...$configurableMethods)
    {
        $this->invocationHandler   = $handler;
        $this->matcher             = $matcher;
        $this->configurableMethods = $configurableMethods;
    }

    /**
     * @throws MatcherAlreadyRegisteredException
     *
     * @return $this
     */
    public function id(string $id): self
    {
        $this->invocationHandler->registerMatcher($id, $this->matcher);

        return $this;
    }

    /**
     * @return $this
     */
    public function will(Stub $stub): Identity
    {
        $this->matcher->setStub($stub);

        return $this;
    }

    /**
     * @throws IncompatibleReturnValueException
     */
    public function willReturn(mixed $value, mixed ...$nextValues): self
    {
        if (count($nextValues) === 0) {
            $this->ensureTypeOfReturnValues([$value]);

            $stub = $value instanceof Stub ? $value : new ReturnStub($value);

            return $this->will($stub);
        }

        $values = array_merge([$value], $nextValues);

        $this->ensureTypeOfReturnValues($values);

        $stub = new ConsecutiveCalls($values);

        return $this->will($stub);
    }

    public function willReturnReference(mixed &$reference): self
    {
        $stub = new ReturnReference($reference);

        return $this->will($stub);
    }

    public function willReturnMap(array $valueMap): self
    {
        $method = $this->configuredMethod();

        assert($method instanceof ConfigurableMethod);

        $numberOfParameters = $method->numberOfParameters();
        $defaultValues      = $method->defaultParameterValues();
        $hasDefaultValues   = !empty($defaultValues);

        $_valueMap = [];

        foreach ($valueMap as $mapping) {
            $numberOfConfiguredParameters = count($mapping) - 1;

            if ($numberOfConfiguredParameters === $numberOfParameters || !$hasDefaultValues) {
                $_valueMap[] = $mapping;

                continue;
            }

            $_mapping    = [];
            $returnValue = array_pop($mapping);

            foreach (range(0, $numberOfParameters - 1) as $i) {
                if (array_key_exists($i, $mapping)) {
                    $_mapping[] = $mapping[$i];

                    continue;
                }

                if (array_key_exists($i, $defaultValues)) {
                    $_mapping[] = $defaultValues[$i];
                }
            }

            $_mapping[]  = $returnValue;
            $_valueMap[] = $_mapping;
        }

        $stub = new ReturnValueMap($_valueMap);

        return $this->will($stub);
    }

    public function willReturnArgument(int $argumentIndex): self
    {
        $stub = new ReturnArgument($argumentIndex);

        return $this->will($stub);
    }

    public function willReturnCallback(callable $callback): self
    {
        $stub = new ReturnCallback($callback);

        return $this->will($stub);
    }

    public function willReturnSelf(): self
    {
        $stub = new ReturnSelf;

        return $this->will($stub);
    }

    public function willReturnOnConsecutiveCalls(mixed ...$values): self
    {
        $stub = new ConsecutiveCalls($values);

        return $this->will($stub);
    }

    public function willThrowException(Throwable $exception): self
    {
        $stub = new Exception($exception);

        return $this->will($stub);
    }

    /**
     * @return $this
     */
    public function after(string $id): self
    {
        $this->matcher->setAfterMatchBuilderId($id);

        return $this;
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function with(mixed ...$arguments): self
    {
        $this->ensureParametersCanBeConfigured();

        $this->matcher->setParametersRule(new Rule\Parameters($arguments));

        return $this;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     *
     * @return $this
     */
    public function withAnyParameters(): self
    {
        $this->ensureParametersCanBeConfigured();

        $this->matcher->setParametersRule(new Rule\AnyParameters);

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     * @throws MethodCannotBeConfiguredException
     * @throws MethodNameAlreadyConfiguredException
     *
     * @return $this
     */
    public function method(Constraint|PropertyHook|string $constraint): self
    {
        if ($this->matcher->hasMethodNameRule()) {
            throw new MethodNameAlreadyConfiguredException;
        }

        if ($constraint instanceof PropertyHook) {
            $constraint = $constraint->asString();
        }

        if (is_string($constraint)) {
            $this->configurableMethodNames ??= array_flip(
                array_map(
                    static fn (ConfigurableMethod $configurable) => strtolower($configurable->name()),
                    $this->configurableMethods,
                ),
            );

            if (!array_key_exists(strtolower($constraint), $this->configurableMethodNames)) {
                throw new MethodCannotBeConfiguredException($constraint);
            }
        }

        $this->matcher->setMethodNameRule(new Rule\MethodName($constraint));

        return $this;
    }

    /**
     * @throws MethodNameNotConfiguredException
     * @throws MethodParametersAlreadyConfiguredException
     */
    private function ensureParametersCanBeConfigured(): void
    {
        if (!$this->matcher->hasMethodNameRule()) {
            throw new MethodNameNotConfiguredException;
        }

        if ($this->matcher->hasParametersRule()) {
            throw new MethodParametersAlreadyConfiguredException;
        }
    }

    private function configuredMethod(): ?ConfigurableMethod
    {
        $configuredMethod = null;

        foreach ($this->configurableMethods as $configurableMethod) {
            if ($this->matcher->methodNameRule()->matchesName($configurableMethod->name())) {
                if ($configuredMethod !== null) {
                    return null;
                }

                $configuredMethod = $configurableMethod;
            }
        }

        return $configuredMethod;
    }

    /**
     * @param array<mixed> $values
     *
     * @throws IncompatibleReturnValueException
     */
    private function ensureTypeOfReturnValues(array $values): void
    {
        $configuredMethod = $this->configuredMethod();

        if ($configuredMethod === null) {
            return;
        }

        foreach ($values as $value) {
            if (!$configuredMethod->mayReturn($value)) {
                throw new IncompatibleReturnValueException(
                    $configuredMethod,
                    $value,
                );
            }
        }
    }
}
