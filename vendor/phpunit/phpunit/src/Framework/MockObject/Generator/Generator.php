<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Generator;

use const PHP_EOL;
use const PHP_MAJOR_VERSION;
use const PHP_MINOR_VERSION;
use const PHP_VERSION;
use const PREG_OFFSET_CAPTURE;
use const WSDL_CACHE_NONE;
use function array_merge;
use function array_pop;
use function array_unique;
use function assert;
use function class_exists;
use function count;
use function explode;
use function extension_loaded;
use function implode;
use function in_array;
use function interface_exists;
use function is_array;
use function is_object;
use function md5;
use function mt_rand;
use function preg_match;
use function preg_match_all;
use function range;
use function serialize;
use function sort;
use function sprintf;
use function str_contains;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trait_exists;
use function version_compare;
use Exception;
use Iterator;
use IteratorAggregate;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Event\Code\TestMethodBuilder;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\ConfigurableMethod;
use PHPUnit\Framework\MockObject\DoubledCloneMethod;
use PHPUnit\Framework\MockObject\ErrorCloneMethod;
use PHPUnit\Framework\MockObject\GeneratedAsMockObject;
use PHPUnit\Framework\MockObject\GeneratedAsTestStub;
use PHPUnit\Framework\MockObject\Method;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\MockObjectApi;
use PHPUnit\Framework\MockObject\MockObjectInternal;
use PHPUnit\Framework\MockObject\MutableStubApi;
use PHPUnit\Framework\MockObject\ProxiedCloneMethod;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\MockObject\StubApi;
use PHPUnit\Framework\MockObject\StubInternal;
use PHPUnit\Framework\MockObject\TestDoubleState;
use PropertyHookType;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use SebastianBergmann\Type\ReflectionMapper;
use SebastianBergmann\Type\Type;
use SoapClient;
use SoapFault;
use Throwable;
use Traversable;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Generator
{
    use TemplateLoader;

    /**
     * @var array<non-empty-string, true>
     */
    private static $excludedMethodNames = [];

    /**
     * @var array<non-empty-string, MockClass>
     */
    private static array $cache = [];

    /**
     * Returns a test double for the specified class.
     *
     * @param ?list<non-empty-string> $methods
     * @param list<mixed>             $arguments
     *
     * @throws ClassIsEnumerationException
     * @throws ClassIsFinalException
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     * @throws NameAlreadyInUseException
     * @throws OriginalConstructorInvocationRequiredException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTypeException
     */
    public function testDouble(string $type, bool $mockObject, bool $markAsMockObject, ?array $methods = [], array $arguments = [], string $mockClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true, bool $cloneArguments = true, bool $callOriginalMethods = false, ?object $proxyTarget = null, bool $allowMockingUnknownTypes = true, bool $returnValueGeneration = true): MockObject|Stub
    {
        if ($type === Traversable::class) {
            $type = Iterator::class;
        }

        if (!$allowMockingUnknownTypes) {
            $this->ensureKnownType($type, $callAutoload);
        }

        $this->ensureValidMethods($methods);
        $this->ensureNameForTestDoubleClassIsAvailable($mockClassName);

        if (!$callOriginalConstructor && $callOriginalMethods) {
            throw new OriginalConstructorInvocationRequiredException;
        }

        $mock = $this->generate(
            $type,
            $mockObject,
            $markAsMockObject,
            $methods,
            $mockClassName,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments,
            $callOriginalMethods,
        );

        $object = $this->instantiate(
            $mock,
            $type,
            $callOriginalConstructor,
            $arguments,
            $callOriginalMethods,
            $proxyTarget,
            $returnValueGeneration,
        );

        assert($object instanceof $type);

        if ($mockObject) {
            assert($object instanceof MockObject);
        } else {
            assert($object instanceof Stub);
        }

        return $object;
    }

    /**
     * @param list<class-string> $interfaces
     *
     * @throws RuntimeException
     * @throws UnknownInterfaceException
     */
    public function testDoubleForInterfaceIntersection(array $interfaces, bool $mockObject, bool $callAutoload = true, bool $returnValueGeneration = true): MockObject|Stub
    {
        if (count($interfaces) < 2) {
            throw new RuntimeException('At least two interfaces must be specified');
        }

        foreach ($interfaces as $interface) {
            if (!interface_exists($interface, $callAutoload)) {
                throw new UnknownInterfaceException($interface);
            }
        }

        sort($interfaces);

        $methods = [];

        foreach ($interfaces as $interface) {
            $methods = array_merge($methods, $this->namesOfMethodsIn($interface));
        }

        if (count(array_unique($methods)) < count($methods)) {
            throw new RuntimeException('Interfaces must not declare the same method');
        }

        $unqualifiedNames = [];

        foreach ($interfaces as $interface) {
            $parts              = explode('\\', $interface);
            $unqualifiedNames[] = array_pop($parts);
        }

        sort($unqualifiedNames);

        do {
            $intersectionName = sprintf(
                'Intersection_%s_%s',
                implode('_', $unqualifiedNames),
                substr(md5((string) mt_rand()), 0, 8),
            );
        } while (interface_exists($intersectionName, false));

        $template = $this->loadTemplate('intersection.tpl');

        $template->setVar(
            [
                'intersection' => $intersectionName,
                'interfaces'   => implode(', ', $interfaces),
            ],
        );

        eval($template->render());

        return $this->testDouble(
            $intersectionName,
            $mockObject,
            $mockObject,
            returnValueGeneration: $returnValueGeneration,
        );
    }

    /**
     * Returns a mock object for the specified abstract class with all abstract
     * methods of the class mocked.
     *
     * Concrete methods to mock can be specified with the $mockedMethods parameter.
     *
     * @param list<mixed>             $arguments
     * @param ?list<non-empty-string> $mockedMethods
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
     * @throws UnknownClassException
     * @throws UnknownTypeException
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5241
     */
    public function mockObjectForAbstractClass(string $originalClassName, array $arguments = [], string $mockClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true, ?array $mockedMethods = null, bool $cloneArguments = true): MockObject
    {
        if (class_exists($originalClassName, $callAutoload) ||
            interface_exists($originalClassName, $callAutoload)) {
            $reflector = $this->reflectClass($originalClassName);
            $methods   = $mockedMethods;

            foreach ($reflector->getMethods() as $method) {
                if ($method->isAbstract() && !in_array($method->getName(), $methods ?? [], true)) {
                    $methods[] = $method->getName();
                }
            }

            if (empty($methods)) {
                $methods = null;
            }

            $mockObject = $this->testDouble(
                $originalClassName,
                true,
                true,
                $methods,
                $arguments,
                $mockClassName,
                $callOriginalConstructor,
                $callOriginalClone,
                $callAutoload,
                $cloneArguments,
            );

            assert($mockObject instanceof $originalClassName);
            assert($mockObject instanceof MockObject);

            return $mockObject;
        }

        throw new UnknownClassException($originalClassName);
    }

    /**
     * Returns a mock object for the specified trait with all abstract methods
     * of the trait mocked. Concrete methods to mock can be specified with the
     * `$mockedMethods` parameter.
     *
     * @param trait-string            $traitName
     * @param list<mixed>             $arguments
     * @param ?list<non-empty-string> $mockedMethods
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
     * @throws UnknownClassException
     * @throws UnknownTraitException
     * @throws UnknownTypeException
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5243
     */
    public function mockObjectForTrait(string $traitName, array $arguments = [], string $mockClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true, ?array $mockedMethods = null, bool $cloneArguments = true): MockObject
    {
        if (!trait_exists($traitName, $callAutoload)) {
            throw new UnknownTraitException($traitName);
        }

        $className = $this->generateClassName(
            $traitName,
            '',
            'Trait_',
        );

        $classTemplate = $this->loadTemplate('trait_class.tpl');

        $classTemplate->setVar(
            [
                'prologue'   => 'abstract ',
                'class_name' => $className['className'],
                'trait_name' => $traitName,
            ],
        );

        $mockTrait = new MockTrait($classTemplate->render(), $className['className']);
        $mockTrait->generate();

        return $this->mockObjectForAbstractClass($className['className'], $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $mockedMethods, $cloneArguments);
    }

    /**
     * Returns an object for the specified trait.
     *
     * @param trait-string $traitName
     * @param list<mixed>  $arguments
     *
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws UnknownTraitException
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5244
     */
    public function objectForTrait(string $traitName, string $traitClassName = '', bool $callAutoload = true, bool $callOriginalConstructor = false, array $arguments = []): object
    {
        if (!trait_exists($traitName, $callAutoload)) {
            throw new UnknownTraitException($traitName);
        }

        $className = $this->generateClassName(
            $traitName,
            $traitClassName,
            'Trait_',
        );

        $classTemplate = $this->loadTemplate('trait_class.tpl');

        $classTemplate->setVar(
            [
                'prologue'   => '',
                'class_name' => $className['className'],
                'trait_name' => $traitName,
            ],
        );

        return $this->instantiate(
            new MockTrait(
                $classTemplate->render(),
                $className['className'],
            ),
            '',
            $callOriginalConstructor,
            $arguments,
        );
    }

    /**
     * @param ?list<non-empty-string> $methods
     *
     * @throws ClassIsEnumerationException
     * @throws ClassIsFinalException
     * @throws ReflectionException
     * @throws RuntimeException
     *
     * @todo This method is only public because it is used to test generated code in PHPT tests
     *
     * @see https://github.com/sebastianbergmann/phpunit/issues/5476
     */
    public function generate(string $type, bool $mockObject, bool $markAsMockObject, ?array $methods = null, string $mockClassName = '', bool $callOriginalClone = true, bool $callAutoload = true, bool $cloneArguments = true, bool $callOriginalMethods = false): MockClass
    {
        if ($mockClassName !== '') {
            return $this->generateCodeForTestDoubleClass(
                $type,
                $mockObject,
                $markAsMockObject,
                $methods,
                $mockClassName,
                $callOriginalClone,
                $callAutoload,
                $cloneArguments,
                $callOriginalMethods,
            );
        }

        $key = md5(
            $type .
            ($mockObject ? 'MockObject' : 'TestStub') .
            ($markAsMockObject ? 'MockObject' : 'TestStub') .
            serialize($methods) .
            serialize($callOriginalClone) .
            serialize($cloneArguments) .
            serialize($callOriginalMethods),
        );

        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = $this->generateCodeForTestDoubleClass(
                $type,
                $mockObject,
                $markAsMockObject,
                $methods,
                $mockClassName,
                $callOriginalClone,
                $callAutoload,
                $cloneArguments,
                $callOriginalMethods,
            );
        }

        return self::$cache[$key];
    }

    /**
     * @param non-empty-string       $wsdlFile
     * @param class-string           $className
     * @param list<non-empty-string> $methods
     * @param array<mixed>           $options
     *
     * @throws RuntimeException
     * @throws SoapExtensionNotAvailableException
     *
     * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5242
     */
    public function generateClassFromWsdl(string $wsdlFile, string $className, array $methods = [], array $options = []): string
    {
        if (!extension_loaded('soap')) {
            throw new SoapExtensionNotAvailableException;
        }

        $options['cache_wsdl'] = WSDL_CACHE_NONE;

        try {
            $client   = new SoapClient($wsdlFile, $options);
            $_methods = array_unique($client->__getFunctions() ?? []);

            unset($client);
        } catch (SoapFault $e) {
            throw new RuntimeException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }

        sort($_methods);

        $methodTemplate = $this->loadTemplate('wsdl_method.tpl');
        $methodsBuffer  = '';

        foreach ($_methods as $method) {
            preg_match_all('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\(/', $method, $matches, PREG_OFFSET_CAPTURE);

            $lastFunction = array_pop($matches[0]);
            $nameStart    = $lastFunction[1];
            $nameEnd      = $nameStart + strlen($lastFunction[0]) - 1;
            $name         = str_replace('(', '', $lastFunction[0]);

            if (empty($methods) || in_array($name, $methods, true)) {
                $arguments = explode(
                    ',',
                    str_replace(')', '', substr($method, $nameEnd + 1)),
                );

                foreach (range(0, count($arguments) - 1) as $i) {
                    $parameterStart = strpos($arguments[$i], '$');

                    if (!$parameterStart) {
                        continue;
                    }

                    $arguments[$i] = substr($arguments[$i], $parameterStart);
                }

                $methodTemplate->setVar(
                    [
                        'method_name' => $name,
                        'arguments'   => implode(', ', $arguments),
                    ],
                );

                $methodsBuffer .= $methodTemplate->render();
            }
        }

        $optionsBuffer = '[';

        foreach ($options as $key => $value) {
            $optionsBuffer .= $key . ' => ' . $value;
        }

        $optionsBuffer .= ']';

        $classTemplate = $this->loadTemplate('wsdl_class.tpl');
        $namespace     = '';

        if (str_contains($className, '\\')) {
            $parts     = explode('\\', $className);
            $className = array_pop($parts);
            $namespace = 'namespace ' . implode('\\', $parts) . ';' . "\n\n";
        }

        $classTemplate->setVar(
            [
                'namespace'  => $namespace,
                'class_name' => $className,
                'wsdl'       => $wsdlFile,
                'options'    => $optionsBuffer,
                'methods'    => $methodsBuffer,
            ],
        );

        return $classTemplate->render();
    }

    /**
     * @throws ReflectionException
     *
     * @return list<MockMethod>
     */
    public function mockClassMethods(string $className, bool $callOriginalMethods, bool $cloneArguments): array
    {
        $class   = $this->reflectClass($className);
        $methods = [];

        foreach ($class->getMethods() as $method) {
            if (($method->isPublic() || $method->isAbstract()) && $this->canMethodBeDoubled($method)) {
                $methods[] = MockMethod::fromReflection($method, $callOriginalMethods, $cloneArguments);
            }
        }

        return $methods;
    }

    /**
     * @param class-string $interfaceName
     *
     * @throws ReflectionException
     *
     * @return list<ReflectionMethod>
     */
    private function userDefinedInterfaceMethods(string $interfaceName): array
    {
        $interface = $this->reflectClass($interfaceName);
        $methods   = [];

        foreach ($interface->getMethods() as $method) {
            if (!$method->isUserDefined()) {
                continue;
            }

            $methods[] = $method;
        }

        return $methods;
    }

    /**
     * @param array<mixed> $arguments
     *
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function instantiate(MockType $mockClass, string $type = '', bool $callOriginalConstructor = false, array $arguments = [], bool $callOriginalMethods = false, ?object $proxyTarget = null, bool $returnValueGeneration = true): object
    {
        $className = $mockClass->generate();

        try {
            $object = (new ReflectionClass($className))->newInstanceWithoutConstructor();
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
            // @codeCoverageIgnoreEnd
        }

        $reflector = new ReflectionObject($object);

        if ($object instanceof StubInternal && $mockClass instanceof MockClass) {
            /**
             * @noinspection PhpUnhandledExceptionInspection
             */
            $reflector->getProperty('__phpunit_state')->setValue(
                $object,
                new TestDoubleState($mockClass->configurableMethods(), $returnValueGeneration),
            );

            if ($callOriginalMethods) {
                $this->instantiateProxyTarget($proxyTarget, $object, $type, $arguments);
            }
        }

        if ($callOriginalConstructor && $reflector->getConstructor() !== null) {
            try {
                $reflector->getConstructor()->invokeArgs($object, $arguments);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new ReflectionException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
                // @codeCoverageIgnoreEnd
            }
        }

        return $object;
    }

    /**
     * @param ?list<non-empty-string> $explicitMethods
     *
     * @throws ClassIsEnumerationException
     * @throws ClassIsFinalException
     * @throws ReflectionException
     * @throws RuntimeException
     */
    private function generateCodeForTestDoubleClass(string $type, bool $mockObject, bool $markAsMockObject, ?array $explicitMethods, string $mockClassName, bool $callOriginalClone, bool $callAutoload, bool $cloneArguments, bool $callOriginalMethods): MockClass
    {
        $classTemplate         = $this->loadTemplate('test_double_class.tpl');
        $additionalInterfaces  = [];
        $doubledCloneMethod    = false;
        $proxiedCloneMethod    = false;
        $isClass               = false;
        $isReadonly            = false;
        $isInterface           = false;
        $class                 = null;
        $mockMethods           = new MockMethodSet;
        $testDoubleClassPrefix = $mockObject ? 'MockObject_' : 'TestStub_';

        $_mockClassName = $this->generateClassName(
            $type,
            $mockClassName,
            $testDoubleClassPrefix,
        );

        if (class_exists($_mockClassName['fullClassName'], $callAutoload)) {
            $isClass = true;
        } elseif (interface_exists($_mockClassName['fullClassName'], $callAutoload)) {
            $isInterface = true;
        }

        if (!$isClass && !$isInterface) {
            $prologue = 'class ' . $_mockClassName['originalClassName'] . "\n{\n}\n\n";

            if (!empty($_mockClassName['namespaceName'])) {
                $prologue = 'namespace ' . $_mockClassName['namespaceName'] .
                            " {\n\n" . $prologue . "}\n\n" .
                            "namespace {\n\n";

                $epilogue = "\n\n}";
            }

            $doubledCloneMethod = true;
        } else {
            $class = $this->reflectClass($_mockClassName['fullClassName']);

            if ($class->isEnum()) {
                throw new ClassIsEnumerationException($_mockClassName['fullClassName']);
            }

            if ($class->isFinal()) {
                throw new ClassIsFinalException($_mockClassName['fullClassName']);
            }

            if ($class->isReadOnly()) {
                $isReadonly = true;
            }

            // @see https://github.com/sebastianbergmann/phpunit/issues/2995
            if ($isInterface && $class->implementsInterface(Throwable::class)) {
                $actualClassName        = Exception::class;
                $additionalInterfaces[] = $class->getName();
                $isInterface            = false;
                $class                  = $this->reflectClass($actualClassName);

                foreach ($this->userDefinedInterfaceMethods($_mockClassName['fullClassName']) as $method) {
                    $methodName = $method->getName();

                    if ($class->hasMethod($methodName)) {
                        $classMethod = $class->getMethod($methodName);

                        if (!$this->canMethodBeDoubled($classMethod)) {
                            continue;
                        }
                    }

                    $mockMethods->addMethods(
                        MockMethod::fromReflection($method, $callOriginalMethods, $cloneArguments),
                    );
                }

                $_mockClassName = $this->generateClassName(
                    $actualClassName,
                    $_mockClassName['className'],
                    $testDoubleClassPrefix,
                );
            }

            // @see https://github.com/sebastianbergmann/phpunit-mock-objects/issues/103
            if ($isInterface && $class->implementsInterface(Traversable::class) &&
                !$class->implementsInterface(Iterator::class) &&
                !$class->implementsInterface(IteratorAggregate::class)) {
                $additionalInterfaces[] = Iterator::class;

                $mockMethods->addMethods(
                    ...$this->mockClassMethods(Iterator::class, $callOriginalMethods, $cloneArguments),
                );
            }

            if ($class->hasMethod('__clone')) {
                $cloneMethod = $class->getMethod('__clone');

                if (!$cloneMethod->isFinal()) {
                    if ($callOriginalClone && !$isInterface) {
                        $proxiedCloneMethod = true;
                    } else {
                        $doubledCloneMethod = true;
                    }
                }
            } else {
                $doubledCloneMethod = true;
            }
        }

        if ($isClass && $explicitMethods === []) {
            $mockMethods->addMethods(
                ...$this->mockClassMethods($_mockClassName['fullClassName'], $callOriginalMethods, $cloneArguments),
            );
        }

        if ($isInterface && ($explicitMethods === [] || $explicitMethods === null)) {
            $mockMethods->addMethods(
                ...$this->interfaceMethods($_mockClassName['fullClassName'], $cloneArguments),
            );
        }

        if (is_array($explicitMethods)) {
            foreach ($explicitMethods as $methodName) {
                if ($class->hasMethod($methodName)) {
                    $method = $class->getMethod($methodName);

                    if ($this->canMethodBeDoubled($method)) {
                        $mockMethods->addMethods(
                            MockMethod::fromReflection($method, $callOriginalMethods, $cloneArguments),
                        );
                    }
                } else {
                    $mockMethods->addMethods(
                        MockMethod::fromName(
                            $_mockClassName['fullClassName'],
                            $methodName,
                            $cloneArguments,
                        ),
                    );
                }
            }
        }

        $propertiesWithHooks = $this->properties($class);
        $configurableMethods = $this->configurableMethods($mockMethods, $propertiesWithHooks);

        $mockedMethods = '';

        foreach ($mockMethods->asArray() as $mockMethod) {
            $mockedMethods .= $mockMethod->generateCode();
        }

        /** @var trait-string[] $traits */
        $traits = [];

        /** @phpstan-ignore identical.alwaysTrue */
        $isPhp82 = PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 2;

        if (!$isReadonly && $isPhp82) {
            // @codeCoverageIgnoreStart
            $traits[] = MutableStubApi::class;
            // @codeCoverageIgnoreEnd
        } else {
            $traits[] = StubApi::class;
        }

        if ($mockObject) {
            $traits[] = MockObjectApi::class;
        }

        if ($markAsMockObject) {
            $traits[] = GeneratedAsMockObject::class;
        } else {
            $traits[] = GeneratedAsTestStub::class;
        }

        if ($mockMethods->hasMethod('method') || (isset($class) && $class->hasMethod('method'))) {
            $message = sprintf(
                '%s %s has a method named "method". Doubling %s that have a method named "method" is deprecated. Support for this will be removed in PHPUnit 12.',
                ($isInterface) ? 'Interface' : 'Class',
                isset($class) ? $class->getName() : $type,
                ($isInterface) ? 'interfaces' : 'classes',
            );

            try {
                EventFacade::emitter()->testTriggeredPhpunitDeprecation(
                    TestMethodBuilder::fromCallStack(),
                    $message,
                );
            } catch (NoTestCaseObjectOnCallStackException) {
                EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation($message);
            }
        }

        if (!$mockMethods->hasMethod('method') && (!isset($class) || !$class->hasMethod('method'))) {
            $traits[] = Method::class;
        }

        if ($isPhp82 && $isReadonly) {
            // @codeCoverageIgnoreStart
            $traits[] = ErrorCloneMethod::class;
            // @codeCoverageIgnoreEnd
        } else {
            if ($doubledCloneMethod) {
                $traits[] = DoubledCloneMethod::class;
            } elseif ($proxiedCloneMethod) {
                $traits[] = ProxiedCloneMethod::class;
            }
        }

        $useStatements = '';

        foreach ($traits as $trait) {
            $useStatements .= sprintf(
                '    use %s;' . PHP_EOL,
                $trait,
            );
        }

        unset($traits);

        $classTemplate->setVar(
            [
                'prologue'          => $prologue ?? '',
                'epilogue'          => $epilogue ?? '',
                'class_declaration' => $this->generateTestDoubleClassDeclaration(
                    $mockObject,
                    $_mockClassName,
                    $isInterface,
                    $additionalInterfaces,
                    $isReadonly,
                ),
                'use_statements'  => $useStatements,
                'mock_class_name' => $_mockClassName['className'],
                'methods'         => $mockedMethods,
                'property_hooks'  => (new HookedPropertyGenerator)->generate(
                    $_mockClassName['className'],
                    $propertiesWithHooks,
                ),
            ],
        );

        return new MockClass(
            $classTemplate->render(),
            $_mockClassName['className'],
            $configurableMethods,
        );
    }

    /**
     * @return array{className: non-empty-string, originalClassName: non-empty-string, fullClassName: non-empty-string, namespaceName: string}
     */
    private function generateClassName(string $type, string $className, string $prefix): array
    {
        if ($type[0] === '\\') {
            $type = substr($type, 1);
        }

        $classNameParts = explode('\\', $type);

        if (count($classNameParts) > 1) {
            $type          = array_pop($classNameParts);
            $namespaceName = implode('\\', $classNameParts);
            $fullClassName = $namespaceName . '\\' . $type;
        } else {
            $namespaceName = '';
            $fullClassName = $type;
        }

        if ($className === '') {
            do {
                $className = $prefix . $type . '_' .
                             substr(md5((string) mt_rand()), 0, 8);
            } while (class_exists($className, false));
        }

        return [
            'className'         => $className,
            'originalClassName' => $type,
            'fullClassName'     => $fullClassName,
            'namespaceName'     => $namespaceName,
        ];
    }

    /**
     * @param array{className: non-empty-string, originalClassName: non-empty-string, fullClassName: non-empty-string, namespaceName: string} $mockClassName
     * @param list<class-string>                                                                                                              $additionalInterfaces
     */
    private function generateTestDoubleClassDeclaration(bool $mockObject, array $mockClassName, bool $isInterface, array $additionalInterfaces, bool $isReadonly): string
    {
        if ($mockObject) {
            $additionalInterfaces[] = MockObjectInternal::class;
        } else {
            $additionalInterfaces[] = StubInternal::class;
        }

        if ($isReadonly) {
            $buffer = 'readonly class ';
        } else {
            $buffer = 'class ';
        }

        $interfaces = implode(', ', $additionalInterfaces);

        if ($isInterface) {
            $buffer .= sprintf(
                '%s implements %s',
                $mockClassName['className'],
                $interfaces,
            );

            if (!in_array($mockClassName['originalClassName'], $additionalInterfaces, true)) {
                $buffer .= ', ';

                if (!empty($mockClassName['namespaceName'])) {
                    $buffer .= $mockClassName['namespaceName'] . '\\';
                }

                $buffer .= $mockClassName['originalClassName'];
            }
        } else {
            $buffer .= sprintf(
                '%s extends %s%s implements %s',
                $mockClassName['className'],
                !empty($mockClassName['namespaceName']) ? $mockClassName['namespaceName'] . '\\' : '',
                $mockClassName['originalClassName'],
                $interfaces,
            );
        }

        return $buffer;
    }

    private function canMethodBeDoubled(ReflectionMethod $method): bool
    {
        if ($method->isConstructor()) {
            return false;
        }

        if ($method->isDestructor()) {
            return false;
        }

        if ($method->isFinal()) {
            return false;
        }

        if ($method->isPrivate()) {
            return false;
        }

        return !$this->isMethodNameExcluded($method->getName());
    }

    private function isMethodNameExcluded(string $name): bool
    {
        if (self::$excludedMethodNames === []) {
            self::$excludedMethodNames = [
                '__CLASS__'       => true,
                '__DIR__'         => true,
                '__FILE__'        => true,
                '__FUNCTION__'    => true,
                '__LINE__'        => true,
                '__METHOD__'      => true,
                '__NAMESPACE__'   => true,
                '__TRAIT__'       => true,
                '__clone'         => true,
                '__halt_compiler' => true,
            ];

            if (version_compare(PHP_VERSION, '8.5', '>=')) {
                self::$excludedMethodNames['__sleep']  = true;
                self::$excludedMethodNames['__wakeup'] = true;
            }
        }

        return isset(self::$excludedMethodNames[$name]);
    }

    /**
     * @throws UnknownTypeException
     */
    private function ensureKnownType(string $type, bool $callAutoload): void
    {
        if (!class_exists($type, $callAutoload) && !interface_exists($type, $callAutoload)) {
            throw new UnknownTypeException($type);
        }
    }

    /**
     * @param ?list<non-empty-string> $methods
     *
     * @throws DuplicateMethodException
     * @throws InvalidMethodNameException
     */
    private function ensureValidMethods(?array $methods): void
    {
        if ($methods === null) {
            return;
        }

        foreach ($methods as $method) {
            if (!preg_match('~[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*~', (string) $method)) {
                throw new InvalidMethodNameException((string) $method);
            }
        }

        if ($methods !== array_unique($methods)) {
            throw new DuplicateMethodException($methods);
        }
    }

    /**
     * @throws NameAlreadyInUseException
     * @throws ReflectionException
     */
    private function ensureNameForTestDoubleClassIsAvailable(string $className): void
    {
        if ($className === '') {
            return;
        }

        if (class_exists($className, false) ||
            interface_exists($className, false) ||
            trait_exists($className, false)) {
            throw new NameAlreadyInUseException($className);
        }
    }

    /**
     * @param class-string $type
     * @param array<mixed> $arguments
     *
     * @throws ReflectionException
     */
    private function instantiateProxyTarget(?object $proxyTarget, object $object, string $type, array $arguments): void
    {
        if (!is_object($proxyTarget)) {
            assert(class_exists($type));

            if (count($arguments) === 0) {
                $proxyTarget = new $type;
            } else {
                $class = new ReflectionClass($type);

                try {
                    $proxyTarget = $class->newInstanceArgs($arguments);
                    // @codeCoverageIgnoreStart
                } catch (\ReflectionException $e) {
                    throw new ReflectionException(
                        $e->getMessage(),
                        $e->getCode(),
                        $e,
                    );
                }
                // @codeCoverageIgnoreEnd
            }
        }

        $object->__phpunit_state()->setProxyTarget($proxyTarget);
    }

    /**
     * @param class-string $className
     *
     * @throws ReflectionException
     *
     * @phpstan-ignore missingType.generics, throws.unusedType
     */
    private function reflectClass(string $className): ReflectionClass
    {
        try {
            $class = new ReflectionClass($className);

            // @codeCoverageIgnoreStart
            /** @phpstan-ignore catch.neverThrown */
        } catch (\ReflectionException $e) {
            throw new ReflectionException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
        // @codeCoverageIgnoreEnd

        return $class;
    }

    /**
     * @param class-string $classOrInterfaceName
     *
     * @throws ReflectionException
     *
     * @return list<string>
     */
    private function namesOfMethodsIn(string $classOrInterfaceName): array
    {
        $class   = $this->reflectClass($classOrInterfaceName);
        $methods = [];

        foreach ($class->getMethods() as $method) {
            if ($method->isPublic() || $method->isAbstract()) {
                $methods[] = $method->getName();
            }
        }

        return $methods;
    }

    /**
     * @param class-string $interfaceName
     *
     * @throws ReflectionException
     *
     * @return list<MockMethod>
     */
    private function interfaceMethods(string $interfaceName, bool $cloneArguments): array
    {
        $class   = $this->reflectClass($interfaceName);
        $methods = [];

        foreach ($class->getMethods() as $method) {
            $methods[] = MockMethod::fromReflection($method, false, $cloneArguments);
        }

        return $methods;
    }

    /**
     * @param list<HookedProperty> $propertiesWithHooks
     *
     * @return list<ConfigurableMethod>
     */
    private function configurableMethods(MockMethodSet $methods, array $propertiesWithHooks): array
    {
        $configurable = [];

        foreach ($methods->asArray() as $method) {
            $configurable[] = new ConfigurableMethod(
                $method->methodName(),
                $method->defaultParameterValues(),
                $method->numberOfParameters(),
                $method->returnType(),
            );
        }

        foreach ($propertiesWithHooks as $property) {
            if ($property->hasGetHook()) {
                $configurable[] = new ConfigurableMethod(
                    sprintf(
                        '$%s::get',
                        $property->name(),
                    ),
                    [],
                    0,
                    $property->type(),
                );
            }

            if ($property->hasSetHook()) {
                $configurable[] = new ConfigurableMethod(
                    sprintf(
                        '$%s::set',
                        $property->name(),
                    ),
                    [],
                    1,
                    Type::fromName('void', false),
                );
            }
        }

        return $configurable;
    }

    /**
     * @param ?ReflectionClass<object> $class
     *
     * @return list<HookedProperty>
     */
    private function properties(?ReflectionClass $class): array
    {
        if (version_compare('8.4.1', PHP_VERSION, '>')) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        if ($class === null) {
            return [];
        }

        $mapper     = new ReflectionMapper;
        $properties = [];

        foreach ($class->getProperties() as $property) {
            if (!$property->isPublic()) {
                continue;
            }

            if ($property->isFinal()) {
                continue;
            }

            if (!$property->hasHooks()) {
                continue;
            }

            $hasGetHook                 = false;
            $hasSetHook                 = false;
            $setHookMethodParameterType = null;

            if ($property->hasHook(PropertyHookType::Get) &&
                !$property->getHook(PropertyHookType::Get)->isFinal()) {
                $hasGetHook = true;
            }

            if ($property->hasHook(PropertyHookType::Set) &&
                !$property->getHook(PropertyHookType::Set)->isFinal()) {
                $hasSetHook                 = true;
                $setHookMethodParameterType = $mapper->fromParameterTypes($property->getHook(PropertyHookType::Set))[0]->type();
            }

            if (!$hasGetHook && !$hasSetHook) {
                continue;
            }

            $properties[] = new HookedProperty(
                $property->getName(),
                $mapper->fromPropertyType($property),
                $hasGetHook,
                $hasSetHook,
                $setHookMethodParameterType,
            );
        }

        return $properties;
    }
}
