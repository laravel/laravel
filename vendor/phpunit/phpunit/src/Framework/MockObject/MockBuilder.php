<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use function array_merge;
use function assert;
use function debug_backtrace;
use function trait_exists;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\Generator\CannotUseAddMethodsException;
use PHPUnit\Framework\MockObject\Generator\ClassIsEnumerationException;
use PHPUnit\Framework\MockObject\Generator\ClassIsFinalException;
use PHPUnit\Framework\MockObject\Generator\DuplicateMethodException;
use PHPUnit\Framework\MockObject\Generator\Generator;
use PHPUnit\Framework\MockObject\Generator\InvalidMethodNameException;
use PHPUnit\Framework\MockObject\Generator\NameAlreadyInUseException;
use PHPUnit\Framework\MockObject\Generator\OriginalConstructorInvocationRequiredException;
use PHPUnit\Framework\MockObject\Generator\ReflectionException;
use PHPUnit\Framework\MockObject\Generator\RuntimeException;
use PHPUnit\Framework\MockObject\Generator\UnknownTypeException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @template MockedType
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class MockBuilder
{
    private readonly TestCase $testCase;

    /**
     * @var class-string|trait-string
     */
    private readonly string $type;

    /**
     * @var list<non-empty-string>
     */
    private array $methods          = [];
    private bool $emptyMethodsArray = false;

    /**
     * @var ?class-string
     */
    private ?string $mockClassName = null;

    /**
     * @var array<mixed>
     */
    private array $constructorArgs         = [];
    private bool $originalConstructor      = true;
    private bool $originalClone            = true;
    private bool $autoload                 = true;
    private bool $cloneArguments           = false;
    private bool $callOriginalMethods      = false;
    private ?object $proxyTarget           = null;
    private bool $allowMockingUnknownTypes = true;
    private bool $returnValueGeneration    = true;
    private readonly Generator $generator;

    /**
     * @param class-string|trait-string $type
     */
    public function __construct(TestCase $testCase, string $type)
    {
        $this->testCase  = $testCase;
        $this->type      = $type;
        $this->generator = new Generator;
    }

    /**
     * Creates a mock object using a fluent interface.
     *
     * @throws ClassIsEnumerationException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws InvalidArgumentException
     * @throws InvalidMethodNameException
     * @throws NameAlreadyInUseException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     *
     * @return MockedType&MockObject
     */
    public function getMock(): MockObject
    {
        $object = $this->generator->testDouble(
            $this->type,
            true,
            true,
            !$this->emptyMethodsArray ? $this->methods : null,
            $this->constructorArgs,
            $this->mockClassName ?? '',
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->cloneArguments,
            $this->callOriginalMethods,
            $this->proxyTarget,
            $this->allowMockingUnknownTypes,
            $this->returnValueGeneration,
        );

        assert($object instanceof $this->type);
        assert($object instanceof MockObject);

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Creates a mock object for an abstract class using a fluent interface.
     *
     * @throws Exception
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @return MockedType&MockObject
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5305
     */
    public function getMockForAbstractClass(): MockObject
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::getMockForAbstractClass() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $object = $this->generator->mockObjectForAbstractClass(
            $this->type,
            $this->constructorArgs,
            $this->mockClassName ?? '',
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->methods,
            $this->cloneArguments,
        );

        assert($object instanceof MockObject);

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Creates a mock object for a trait using a fluent interface.
     *
     * @throws Exception
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @return MockedType&MockObject
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5306
     */
    public function getMockForTrait(): MockObject
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::getMockForTrait() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        assert(trait_exists($this->type));

        $object = $this->generator->mockObjectForTrait(
            $this->type,
            $this->constructorArgs,
            $this->mockClassName ?? '',
            $this->originalConstructor,
            $this->originalClone,
            $this->autoload,
            $this->methods,
            $this->cloneArguments,
        );

        assert($object instanceof MockObject);

        $this->testCase->registerMockObject($object);

        return $object;
    }

    /**
     * Specifies the subset of methods to mock, requiring each to exist in the class.
     *
     * @param list<non-empty-string> $methods
     *
     * @throws CannotUseOnlyMethodsException
     * @throws ReflectionException
     *
     * @return $this
     */
    public function onlyMethods(array $methods): self
    {
        if (empty($methods)) {
            $this->emptyMethodsArray = true;

            return $this;
        }

        try {
            $reflector = new ReflectionClass($this->type);

            // @codeCoverageIgnoreStart
            /** @phpstan-ignore catch.neverThrown */
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ($methods as $method) {
            if (!$reflector->hasMethod($method)) {
                throw new CannotUseOnlyMethodsException($this->type, $method);
            }
        }

        $this->methods = array_merge($this->methods, $methods);

        return $this;
    }

    /**
     * Specifies methods that don't exist in the class which you want to mock.
     *
     * @param list<non-empty-string> $methods
     *
     * @throws CannotUseAddMethodsException
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5320
     */
    public function addMethods(array $methods): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::addMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        if (empty($methods)) {
            $this->emptyMethodsArray = true;

            return $this;
        }

        try {
            $reflector = new ReflectionClass($this->type);

            // @codeCoverageIgnoreStart
            /** @phpstan-ignore catch.neverThrown */
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ($methods as $method) {
            if ($reflector->hasMethod($method)) {
                throw new CannotUseAddMethodsException($this->type, $method);
            }
        }

        $this->methods = array_merge($this->methods, $methods);

        return $this;
    }

    /**
     * Specifies the arguments for the constructor.
     *
     * @param array<mixed> $arguments
     *
     * @return $this
     */
    public function setConstructorArgs(array $arguments): self
    {
        $this->constructorArgs = $arguments;

        return $this;
    }

    /**
     * Specifies the name for the mock class.
     *
     * @param class-string $name
     *
     * @return $this
     */
    public function setMockClassName(string $name): self
    {
        $this->mockClassName = $name;

        return $this;
    }

    /**
     * Disables the invocation of the original constructor.
     *
     * @return $this
     */
    public function disableOriginalConstructor(): self
    {
        $this->originalConstructor = false;

        return $this;
    }

    /**
     * Enables the invocation of the original constructor.
     *
     * @return $this
     */
    public function enableOriginalConstructor(): self
    {
        $this->originalConstructor = true;

        return $this;
    }

    /**
     * Disables the invocation of the original clone constructor.
     *
     * @return $this
     */
    public function disableOriginalClone(): self
    {
        $this->originalClone = false;

        return $this;
    }

    /**
     * Enables the invocation of the original clone constructor.
     *
     * @return $this
     */
    public function enableOriginalClone(): self
    {
        $this->originalClone = true;

        return $this;
    }

    /**
     * Disables the use of class autoloading while creating the mock object.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5309
     *
     * @codeCoverageIgnore
     */
    public function disableAutoload(): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::disableAutoload() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->autoload = false;

        return $this;
    }

    /**
     * Enables the use of class autoloading while creating the mock object.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5309
     */
    public function enableAutoload(): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::enableAutoload() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->autoload = true;

        return $this;
    }

    /**
     * Disables the cloning of arguments passed to mocked methods.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5315
     */
    public function disableArgumentCloning(): self
    {
        if (!$this->calledFromTestCase()) {
            EventFacade::emitter()->testTriggeredPhpunitDeprecation(
                $this->testCase->valueObjectForEvents(),
                'MockBuilder::disableArgumentCloning() is deprecated and will be removed in PHPUnit 12 without replacement.',
            );
        }

        $this->cloneArguments = false;

        return $this;
    }

    /**
     * Enables the cloning of arguments passed to mocked methods.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5315
     */
    public function enableArgumentCloning(): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::enableArgumentCloning() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->cloneArguments = true;

        return $this;
    }

    /**
     * Enables the invocation of the original methods.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5307
     *
     * @codeCoverageIgnore
     */
    public function enableProxyingToOriginalMethods(): self
    {
        if (!$this->calledFromTestCase()) {
            EventFacade::emitter()->testTriggeredPhpunitDeprecation(
                $this->testCase->valueObjectForEvents(),
                'MockBuilder::enableProxyingToOriginalMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
            );
        }

        $this->callOriginalMethods = true;

        return $this;
    }

    /**
     * Disables the invocation of the original methods.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5307
     */
    public function disableProxyingToOriginalMethods(): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::disableProxyingToOriginalMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->callOriginalMethods = false;
        $this->proxyTarget         = null;

        return $this;
    }

    /**
     * Sets the proxy target.
     *
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5307
     *
     * @codeCoverageIgnore
     */
    public function setProxyTarget(object $object): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::setProxyTarget() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->proxyTarget = $object;

        return $this;
    }

    /**
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5308
     */
    public function allowMockingUnknownTypes(): self
    {
        EventFacade::emitter()->testTriggeredPhpunitDeprecation(
            $this->testCase->valueObjectForEvents(),
            'MockBuilder::allowMockingUnknownTypes() is deprecated and will be removed in PHPUnit 12 without replacement.',
        );

        $this->allowMockingUnknownTypes = true;

        return $this;
    }

    /**
     * @return $this
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5308
     */
    public function disallowMockingUnknownTypes(): self
    {
        if (!$this->calledFromTestCase()) {
            EventFacade::emitter()->testTriggeredPhpunitDeprecation(
                $this->testCase->valueObjectForEvents(),
                'MockBuilder::disallowMockingUnknownTypes() is deprecated and will be removed in PHPUnit 12 without replacement.',
            );
        }

        $this->allowMockingUnknownTypes = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableAutoReturnValueGeneration(): self
    {
        $this->returnValueGeneration = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableAutoReturnValueGeneration(): self
    {
        $this->returnValueGeneration = false;

        return $this;
    }

    private function calledFromTestCase(): bool
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, limit: 3)[2];

        return isset($caller['class']) && $caller['class'] === TestCase::class;
    }
}
