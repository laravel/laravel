<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Dependency Injection container.
 *
 * @author Chris Corbyn
 */
class Swift_DependencyContainer
{
    /** Constant for literal value types */
    const TYPE_VALUE = 0x0001;

    /** Constant for new instance types */
    const TYPE_INSTANCE = 0x0010;

    /** Constant for shared instance types */
    const TYPE_SHARED = 0x0100;

    /** Constant for aliases */
    const TYPE_ALIAS = 0x1000;

    /** Singleton instance */
    private static $_instance = null;

    /** The data container */
    private $_store = array();

    /** The current endpoint in the data container */
    private $_endPoint;

    /**
     * Constructor should not be used.
     *
     * Use {@link getInstance()} instead.
     */
    public function __construct()
    {
    }

    /**
     * Returns a singleton of the DependencyContainer.
     *
     * @return Swift_DependencyContainer
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * List the names of all items stored in the Container.
     *
     * @return array
     */
    public function listItems()
    {
        return array_keys($this->_store);
    }

    /**
     * Test if an item is registered in this container with the given name.
     *
     * @see register()
     *
     * @param string $itemName
     *
     * @return bool
     */
    public function has($itemName)
    {
        return array_key_exists($itemName, $this->_store)
            && isset($this->_store[$itemName]['lookupType']);
    }

    /**
     * Lookup the item with the given $itemName.
     *
     * @see register()
     *
     * @param string $itemName
     *
     * @throws Swift_DependencyException If the dependency is not found
     *
     * @return mixed
     */
    public function lookup($itemName)
    {
        if (!$this->has($itemName)) {
            throw new Swift_DependencyException(
                'Cannot lookup dependency "'.$itemName.'" since it is not registered.'
                );
        }

        switch ($this->_store[$itemName]['lookupType']) {
            case self::TYPE_ALIAS:
                return $this->_createAlias($itemName);
            case self::TYPE_VALUE:
                return $this->_getValue($itemName);
            case self::TYPE_INSTANCE:
                return $this->_createNewInstance($itemName);
            case self::TYPE_SHARED:
                return $this->_createSharedInstance($itemName);
        }
    }

    /**
     * Create an array of arguments passed to the constructor of $itemName.
     *
     * @param string $itemName
     *
     * @return array
     */
    public function createDependenciesFor($itemName)
    {
        $args = array();
        if (isset($this->_store[$itemName]['args'])) {
            $args = $this->_resolveArgs($this->_store[$itemName]['args']);
        }

        return $args;
    }

    /**
     * Register a new dependency with $itemName.
     *
     * This method returns the current DependencyContainer instance because it
     * requires the use of the fluid interface to set the specific details for the
     * dependency.
     *
     * @see asNewInstanceOf(), asSharedInstanceOf(), asValue()
     *
     * @param string $itemName
     *
     * @return Swift_DependencyContainer
     */
    public function register($itemName)
    {
        $this->_store[$itemName] = array();
        $this->_endPoint = &$this->_store[$itemName];

        return $this;
    }

    /**
     * Specify the previously registered item as a literal value.
     *
     * {@link register()} must be called before this will work.
     *
     * @param mixed $value
     *
     * @return Swift_DependencyContainer
     */
    public function asValue($value)
    {
        $endPoint = &$this->_getEndPoint();
        $endPoint['lookupType'] = self::TYPE_VALUE;
        $endPoint['value'] = $value;

        return $this;
    }

    /**
     * Specify the previously registered item as an alias of another item.
     *
     * @param string $lookup
     *
     * @return Swift_DependencyContainer
     */
    public function asAliasOf($lookup)
    {
        $endPoint = &$this->_getEndPoint();
        $endPoint['lookupType'] = self::TYPE_ALIAS;
        $endPoint['ref'] = $lookup;

        return $this;
    }

    /**
     * Specify the previously registered item as a new instance of $className.
     *
     * {@link register()} must be called before this will work.
     * Any arguments can be set with {@link withDependencies()},
     * {@link addConstructorValue()} or {@link addConstructorLookup()}.
     *
     * @see withDependencies(), addConstructorValue(), addConstructorLookup()
     *
     * @param string $className
     *
     * @return Swift_DependencyContainer
     */
    public function asNewInstanceOf($className)
    {
        $endPoint = &$this->_getEndPoint();
        $endPoint['lookupType'] = self::TYPE_INSTANCE;
        $endPoint['className'] = $className;

        return $this;
    }

    /**
     * Specify the previously registered item as a shared instance of $className.
     *
     * {@link register()} must be called before this will work.
     *
     * @param string $className
     *
     * @return Swift_DependencyContainer
     */
    public function asSharedInstanceOf($className)
    {
        $endPoint = &$this->_getEndPoint();
        $endPoint['lookupType'] = self::TYPE_SHARED;
        $endPoint['className'] = $className;

        return $this;
    }

    /**
     * Specify a list of injected dependencies for the previously registered item.
     *
     * This method takes an array of lookup names.
     *
     * @see addConstructorValue(), addConstructorLookup()
     *
     * @param array $lookups
     *
     * @return Swift_DependencyContainer
     */
    public function withDependencies(array $lookups)
    {
        $endPoint = &$this->_getEndPoint();
        $endPoint['args'] = array();
        foreach ($lookups as $lookup) {
            $this->addConstructorLookup($lookup);
        }

        return $this;
    }

    /**
     * Specify a literal (non looked up) value for the constructor of the
     * previously registered item.
     *
     * @see withDependencies(), addConstructorLookup()
     *
     * @param mixed $value
     *
     * @return Swift_DependencyContainer
     */
    public function addConstructorValue($value)
    {
        $endPoint = &$this->_getEndPoint();
        if (!isset($endPoint['args'])) {
            $endPoint['args'] = array();
        }
        $endPoint['args'][] = array('type' => 'value', 'item' => $value);

        return $this;
    }

    /**
     * Specify a dependency lookup for the constructor of the previously
     * registered item.
     *
     * @see withDependencies(), addConstructorValue()
     *
     * @param string $lookup
     *
     * @return Swift_DependencyContainer
     */
    public function addConstructorLookup($lookup)
    {
        $endPoint = &$this->_getEndPoint();
        if (!isset($this->_endPoint['args'])) {
            $endPoint['args'] = array();
        }
        $endPoint['args'][] = array('type' => 'lookup', 'item' => $lookup);

        return $this;
    }

    /** Get the literal value with $itemName */
    private function _getValue($itemName)
    {
        return $this->_store[$itemName]['value'];
    }

    /** Resolve an alias to another item */
    private function _createAlias($itemName)
    {
        return $this->lookup($this->_store[$itemName]['ref']);
    }

    /** Create a fresh instance of $itemName */
    private function _createNewInstance($itemName)
    {
        $reflector = new ReflectionClass($this->_store[$itemName]['className']);
        if ($reflector->getConstructor()) {
            return $reflector->newInstanceArgs(
                $this->createDependenciesFor($itemName)
                );
        } else {
            return $reflector->newInstance();
        }
    }

    /** Create and register a shared instance of $itemName */
    private function _createSharedInstance($itemName)
    {
        if (!isset($this->_store[$itemName]['instance'])) {
            $this->_store[$itemName]['instance'] = $this->_createNewInstance($itemName);
        }

        return $this->_store[$itemName]['instance'];
    }

    /** Get the current endpoint in the store */
    private function &_getEndPoint()
    {
        if (!isset($this->_endPoint)) {
            throw new BadMethodCallException(
                'Component must first be registered by calling register()'
                );
        }

        return $this->_endPoint;
    }

    /** Get an argument list with dependencies resolved */
    private function _resolveArgs(array $args)
    {
        $resolved = array();
        foreach ($args as $argDefinition) {
            switch ($argDefinition['type']) {
                case 'lookup':
                    $resolved[] = $this->_lookupRecursive($argDefinition['item']);
                    break;
                case 'value':
                    $resolved[] = $argDefinition['item'];
                    break;
            }
        }

        return $resolved;
    }

    /** Resolve a single dependency with an collections */
    private function _lookupRecursive($item)
    {
        if (is_array($item)) {
            $collection = array();
            foreach ($item as $k => $v) {
                $collection[$k] = $this->_lookupRecursive($v);
            }

            return $collection;
        } else {
            return $this->lookup($item);
        }
    }
}
