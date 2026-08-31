<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use function array_pop;
use function explode;
use function implode;
use function ltrim;

class UndefinedTargetClass implements TargetClassInterface
{
    /**
     * @var class-string
     */
    private $name;

    /**
     * @param class-string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return class-string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param  class-string $name
     * @return self
     */
    public static function factory($name)
    {
        return new self($name);
    }

    /**
     * @return list<class-string>
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @return list<self>
     */
    public function getInterfaces()
    {
        return [];
    }

    /**
     * @return list<Method>
     */
    public function getMethods()
    {
        return [];
    }

    /**
     * @return class-string
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
        $parts = explode('\\', ltrim($this->getName(), '\\'));
        array_pop($parts);
        return implode('\\', $parts);
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
     * @return bool
     */
    public function hasInternalAncestor()
    {
        return false;
    }

    /**
     * @param  class-string $interface
     * @return bool
     */
    public function implementsInterface($interface)
    {
        return false;
    }

    /**
     * @return bool
     */
    public function inNamespace()
    {
        return $this->getNamespaceName() !== '';
    }

    /**
     * @return bool
     */
    public function isAbstract()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isFinal()
    {
        return false;
    }
}
