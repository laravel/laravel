<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use Mockery\Exception;
use Serializable;
use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_pop;
use function array_unique;
use function array_values;
use function class_alias;
use function class_exists;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function interface_exists;
use function is_object;
use function md5;
use function preg_match;
use function serialize;
use function strpos;
use function strtolower;
use function trait_exists;

/**
 * This class describes the configuration of mocks and hides away some of the
 * reflection implementation
 */
class MockConfiguration
{
    /**
     * Instance cache of all methods
     *
     * @var list<Method>
     */
    protected $allMethods = [];

    /**
     * Methods that should specifically not be mocked
     *
     * This is currently populated with stuff we don't know how to deal with, should really be somewhere else
     */
    protected $blackListedMethods = [];

    protected $constantsMap = [];

    /**
     * An instance mock is where we override the original class before it's autoloaded
     *
     * @var bool
     */
    protected $instanceMock = false;

    /**
     * If true, overrides original class destructor
     *
     * @var bool
     */
    protected $mockOriginalDestructor = false;

    /**
     * The class name we'd like to use for a generated mock
     *
     * @var string|null
     */
    protected $name;

    /**
     * Param overrides
     *
     * @var array<string,mixed>
     */
    protected $parameterOverrides = [];

    /**
     * A class that we'd like to mock
     * @var TargetClassInterface|null
     */
    protected $targetClass;

    /**
     * @var class-string|null
     */
    protected $targetClassName;

    /**
     * @var array<class-string>
     */
    protected $targetInterfaceNames = [];

    /**
     * A number of interfaces we'd like to mock, keyed by name to attempt to keep unique
     *
     * @var array<TargetClassInterface>
     */
    protected $targetInterfaces = [];

    /**
     * An object we'd like our mock to proxy to
     *
     * @var object|null
     */
    protected $targetObject;

    /**
     * @var array<string>
     */
    protected $targetTraitNames = [];

    /**
     * A number of traits we'd like to mock, keyed by name to attempt to keep unique
     *
     * @var array<string,DefinedTargetClass>
     */
    protected $targetTraits = [];

    /**
     * If not empty, only these methods will be mocked
     *
     * @var array<string>
     */
    protected $whiteListedMethods = [];

    /**
     * @param array<class-string|object>         $targets
     * @param array<string>                      $blackListedMethods
     * @param array<string>                      $whiteListedMethods
     * @param string|null                        $name
     * @param bool                               $instanceMock
     * @param array<string,mixed>                $parameterOverrides
     * @param bool                               $mockOriginalDestructor
     * @param array<string,array<scalar>|scalar> $constantsMap
     */
    public function __construct(
        array $targets = [],
        array $blackListedMethods = [],
        array $whiteListedMethods = [],
        $name = null,
        $instanceMock = false,
        array $parameterOverrides = [],
        $mockOriginalDestructor = false,
        array $constantsMap = []
    ) {
        $this->addTargets($targets);
        $this->blackListedMethods = $blackListedMethods;
        $this->whiteListedMethods = $whiteListedMethods;
        $this->name = $name;
        $this->instanceMock = $instanceMock;
        $this->parameterOverrides = $parameterOverrides;
        $this->mockOriginalDestructor = $mockOriginalDestructor;
        $this->constantsMap = $constantsMap;
    }

    /**
     * Generate a suitable name based on the config
     *
     * @return string
     */
    public function generateName()
    {
        $nameBuilder = new MockNameBuilder();

        $targetObject = $this->getTargetObject();
        if ($targetObject !== null) {
            $className = get_class($targetObject);

            $nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
        }

        $targetClass = $this->getTargetClass();
        if ($targetClass instanceof TargetClassInterface) {
            $className = $targetClass->getName();

            $nameBuilder->addPart(strpos($className, '@') !== false ? md5($className) : $className);
        }

        foreach ($this->getTargetInterfaces() as $targetInterface) {
            $nameBuilder->addPart($targetInterface->getName());
        }

        return $nameBuilder->build();
    }

    /**
     * @return array<string>
     */
    public function getBlackListedMethods()
    {
        return $this->blackListedMethods;
    }

    /**
     * @return array<string,scalar|array<scalar>>
     */
    public function getConstantsMap()
    {
        return $this->constantsMap;
    }

    /**
     * Attempt to create a hash of the configuration, in order to allow caching
     *
     * @TODO workout if this will work
     *
     * @return string
     */
    public function getHash()
    {
        $vars = [
            'targetClassName' => $this->targetClassName,
            'targetInterfaceNames' => $this->targetInterfaceNames,
            'targetTraitNames' => $this->targetTraitNames,
            'name' => $this->name,
            'blackListedMethods' => $this->blackListedMethods,
            'whiteListedMethod' => $this->whiteListedMethods,
            'instanceMock' => $this->instanceMock,
            'parameterOverrides' => $this->parameterOverrides,
            'mockOriginalDestructor' => $this->mockOriginalDestructor,
        ];

        return md5(serialize($vars));
    }

    /**
     * Gets a list of methods from the classes, interfaces and objects and filters them appropriately.
     * Lot's of filtering going on, perhaps we could have filter classes to iterate through
     *
     * @return list<Method>
     */
    public function getMethodsToMock()
    {
        $methods = $this->getAllMethods();

        foreach ($methods as $key => $method) {
            if ($method->isFinal()) {
                unset($methods[$key]);
            }
        }

        /**
         * Whitelist trumps everything else
         */
        $whiteListedMethods = $this->getWhiteListedMethods();
        if ($whiteListedMethods !== []) {
            $whitelist = array_map('strtolower', $whiteListedMethods);

            return array_filter($methods, static function ($method) use ($whitelist) {
                if ($method->isAbstract()) {
                    return true;
                }

                return in_array(strtolower($method->getName()), $whitelist, true);
            });
        }

        /**
         * Remove blacklisted methods
         */
        $blackListedMethods = $this->getBlackListedMethods();
        if ($blackListedMethods !== []) {
            $blacklist = array_map('strtolower', $blackListedMethods);

            $methods = array_filter($methods, static function ($method) use ($blacklist) {
                return ! in_array(strtolower($method->getName()), $blacklist, true);
            });
        }

        /**
         * Internal objects can not be instantiated with newInstanceArgs and if
         * they implement Serializable, unserialize will have to be called. As
         * such, we can't mock it and will need a pass to add a dummy
         * implementation
         */
        $targetClass = $this->getTargetClass();

        if (
            $targetClass !== null
            && $targetClass->implementsInterface(Serializable::class)
            && $targetClass->hasInternalAncestor()
        ) {
            $methods = array_filter($methods, static function ($method) {
                return $method->getName() !== 'unserialize';
            });
        }

        return array_values($methods);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespaceName()
    {
        $parts = explode('\\', $this->getName());
        array_pop($parts);

        if ($parts !== []) {
            return implode('\\', $parts);
        }

        return '';
    }

    /**
     * @return array<string,mixed>
     */
    public function getParameterOverrides()
    {
        return $this->parameterOverrides;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        $parts = explode('\\', $this->getName());
        return array_pop($parts);
    }

    /**
     * @return null|TargetClassInterface
     */
    public function getTargetClass()
    {
        if ($this->targetClass) {
            return $this->targetClass;
        }

        if (! $this->targetClassName) {
            return null;
        }

        if (class_exists($this->targetClassName)) {
            $alias = null;
            if (strpos($this->targetClassName, '@') !== false) {
                $alias = (new MockNameBuilder())
                    ->addPart('anonymous_class')
                    ->addPart(md5($this->targetClassName))
                    ->build();
                class_alias($this->targetClassName, $alias);
            }

            $dtc = DefinedTargetClass::factory($this->targetClassName, $alias);

            if ($this->getTargetObject() === null && $dtc->isFinal()) {
                throw new Exception(
                    'The class ' . $this->targetClassName . ' is marked final and its methods'
                    . ' cannot be replaced. Classes marked final can be passed in'
                    . ' to \Mockery::mock() as instantiated objects to create a'
                    . ' partial mock, but only if the mock is not subject to type'
                    . ' hinting checks.'
                );
            }

            $this->targetClass = $dtc;
        } else {
            $this->targetClass = UndefinedTargetClass::factory($this->targetClassName);
        }

        return $this->targetClass;
    }

    /**
     * @return class-string|null
     */
    public function getTargetClassName()
    {
        return $this->targetClassName;
    }

    /**
     * @return list<TargetClassInterface>
     */
    public function getTargetInterfaces()
    {
        if ($this->targetInterfaces !== []) {
            return $this->targetInterfaces;
        }

        foreach ($this->targetInterfaceNames as $targetInterface) {
            if (! interface_exists($targetInterface)) {
                $this->targetInterfaces[] = UndefinedTargetClass::factory($targetInterface);
                continue;
            }

            $dtc = DefinedTargetClass::factory($targetInterface);
            $extendedInterfaces = array_keys($dtc->getInterfaces());
            $extendedInterfaces[] = $targetInterface;

            $traversableFound = false;
            $iteratorShiftedToFront = false;
            foreach ($extendedInterfaces as $interface) {
                if (! $traversableFound && preg_match('/^\\?Iterator(|Aggregate)$/i', $interface)) {
                    break;
                }

                if (preg_match('/^\\\\?IteratorAggregate$/i', $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
                    $iteratorShiftedToFront = true;

                    continue;
                }

                if (preg_match('/^\\\\?Iterator$/i', $interface)) {
                    $this->targetInterfaces[] = DefinedTargetClass::factory('\\Iterator');
                    $iteratorShiftedToFront = true;

                    continue;
                }

                if (preg_match('/^\\\\?Traversable$/i', $interface)) {
                    $traversableFound = true;
                }
            }

            if ($traversableFound && ! $iteratorShiftedToFront) {
                $this->targetInterfaces[] = DefinedTargetClass::factory('\\IteratorAggregate');
            }

            /**
             * We never straight up implement Traversable
             */
            $isTraversable = preg_match('/^\\\\?Traversable$/i', $targetInterface);
            if ($isTraversable === 0 || $isTraversable === false) {
                $this->targetInterfaces[] = $dtc;
            }
        }

        return $this->targetInterfaces = array_unique($this->targetInterfaces);
    }

    /**
     * @return object|null
     */
    public function getTargetObject()
    {
        return $this->targetObject;
    }

    /**
     * @return list<TargetClassInterface>
     */
    public function getTargetTraits()
    {
        if ($this->targetTraits !== []) {
            return $this->targetTraits;
        }

        foreach ($this->targetTraitNames as $targetTrait) {
            $this->targetTraits[] = DefinedTargetClass::factory($targetTrait);
        }

        $this->targetTraits = array_unique($this->targetTraits); // just in case
        return $this->targetTraits;
    }

    /**
     * @return array<string>
     */
    public function getWhiteListedMethods()
    {
        return $this->whiteListedMethods;
    }

    /**
     * @return bool
     */
    public function isInstanceMock()
    {
        return $this->instanceMock;
    }

    /**
     * @return bool
     */
    public function isMockOriginalDestructor()
    {
        return $this->mockOriginalDestructor;
    }

    /**
     * @param  class-string $className
     * @return self
     */
    public function rename($className)
    {
        $targets = [];

        if ($this->targetClassName) {
            $targets[] = $this->targetClassName;
        }

        if ($this->targetInterfaceNames) {
            $targets = array_merge($targets, $this->targetInterfaceNames);
        }

        if ($this->targetTraitNames) {
            $targets = array_merge($targets, $this->targetTraitNames);
        }

        if ($this->targetObject) {
            $targets[] = $this->targetObject;
        }

        return new self(
            $targets,
            $this->blackListedMethods,
            $this->whiteListedMethods,
            $className,
            $this->instanceMock,
            $this->parameterOverrides,
            $this->mockOriginalDestructor,
            $this->constantsMap
        );
    }

    /**
     * We declare the __callStatic method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     *
     * @return bool
     */
    public function requiresCallStaticTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ($method->getName() === '__callStatic') {
                $params = $method->getParameters();

                if (! array_key_exists(1, $params)) {
                    return false;
                }

                return ! $params[1]->isArray();
            }
        }

        return false;
    }

    /**
     * We declare the __call method to handle undefined stuff, if the class
     * we're mocking has also defined it, we need to comply with their interface
     *
     * @return bool
     */
    public function requiresCallTypeHintRemoval()
    {
        foreach ($this->getAllMethods() as $method) {
            if ($method->getName() === '__call') {
                $params = $method->getParameters();
                return ! $params[1]->isArray();
            }
        }

        return false;
    }

    /**
     * @param class-string|object $target
     */
    protected function addTarget($target)
    {
        if (is_object($target)) {
            $this->setTargetObject($target);
            $this->setTargetClassName(get_class($target));
            return;
        }

        if ($target[0] !== '\\') {
            $target = '\\' . $target;
        }

        if (class_exists($target)) {
            $this->setTargetClassName($target);
            return;
        }

        if (interface_exists($target)) {
            $this->addTargetInterfaceName($target);
            return;
        }

        if (trait_exists($target)) {
            $this->addTargetTraitName($target);
            return;
        }

        /**
         * Default is to set as class, or interface if class already set
         *
         * Don't like this condition, can't remember what the default
         * targetClass is for
         */
        if ($this->getTargetClassName()) {
            $this->addTargetInterfaceName($target);
            return;
        }

        $this->setTargetClassName($target);
    }

    /**
     * If we attempt to implement Traversable,
     * we must ensure we are also implementing either Iterator or IteratorAggregate,
     * and that whichever one it is comes before Traversable in the list of implements.
     *
     * @param class-string $targetInterface
     */
    protected function addTargetInterfaceName($targetInterface)
    {
        $this->targetInterfaceNames[] = $targetInterface;
    }

    /**
     * @param array<class-string> $interfaces
     */
    protected function addTargets($interfaces)
    {
        foreach ($interfaces as $interface) {
            $this->addTarget($interface);
        }
    }

    /**
     * @param class-string $targetTraitName
     */
    protected function addTargetTraitName($targetTraitName)
    {
        $this->targetTraitNames[] = $targetTraitName;
    }

    /**
     * @return list<Method>
     */
    protected function getAllMethods()
    {
        if ($this->allMethods) {
            return $this->allMethods;
        }

        $classes = $this->getTargetInterfaces();

        if ($this->getTargetClass()) {
            $classes[] = $this->getTargetClass();
        }

        $methods = [];
        foreach ($classes as $class) {
            $methods = array_merge($methods, $class->getMethods());
        }

        foreach ($this->getTargetTraits() as $trait) {
            foreach ($trait->getMethods() as $method) {
                if ($method->isAbstract()) {
                    $methods[] = $method;
                }
            }
        }

        $names = [];
        $methods = array_filter($methods, static function ($method) use (&$names) {
            if (in_array($method->getName(), $names, true)) {
                return false;
            }

            $names[] = $method->getName();
            return true;
        });

        return $this->allMethods = $methods;
    }

    /**
     * @param class-string $targetClassName
     */
    protected function setTargetClassName($targetClassName)
    {
        $this->targetClassName = $targetClassName;
    }

    /**
     * @param object $object
     */
    protected function setTargetObject($object)
    {
        $this->targetObject = $object;
    }
}
