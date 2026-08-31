<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function array_merge;
use function array_unique;

use const PHP_VERSION_ID;

class DefinedTargetClass implements TargetClassInterface
{
    /**
     * @var class-string
     */
    private $name;

    /**
     * @var ReflectionClass
     */
    private $rfc;

    /**
     * @param ReflectionClass   $rfc
     * @param class-string|null $alias
     */
    public function __construct(ReflectionClass $rfc, $alias = null)
    {
        $this->rfc = $rfc;
        $this->name = $alias ?? $rfc->getName();
    }

    /**
     * @return class-string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param  class-string      $name
     * @param  class-string|null $alias
     * @return self
     */
    public static function factory($name, $alias = null)
    {
        return new self(new ReflectionClass($name), $alias);
    }

    /**
     * @return list<class-string>
     */
    public function getAttributes()
    {
        if (PHP_VERSION_ID < 80000) {
            return [];
        }

        return array_unique(
            array_merge(
                ['\AllowDynamicProperties'],
                array_map(
                    static function (ReflectionAttribute $attribute): string {
                        return '\\' . $attribute->getName();
                    },
                    $this->rfc->getAttributes()
                )
            )
        );
    }

    /**
     * @return array<class-string,self>
     */
    public function getInterfaces()
    {
        return array_map(
            static function (ReflectionClass $interface): self {
                return new self($interface);
            },
            $this->rfc->getInterfaces()
        );
    }

    /**
     * @return list<Method>
     */
    public function getMethods()
    {
        return array_map(
            static function (ReflectionMethod $method): Method {
                return new Method($method);
            },
            $this->rfc->getMethods()
        );
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
        return $this->rfc->getNamespaceName();
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->rfc->getShortName();
    }

    /**
     * @return bool
     */
    public function hasInternalAncestor()
    {
        if ($this->rfc->isInternal()) {
            return true;
        }

        $child = $this->rfc;
        while ($parent = $child->getParentClass()) {
            if ($parent->isInternal()) {
                return true;
            }

            $child = $parent;
        }

        return false;
    }

    /**
     * @param  class-string $interface
     * @return bool
     */
    public function implementsInterface($interface)
    {
        return $this->rfc->implementsInterface($interface);
    }

    /**
     * @return bool
     */
    public function inNamespace()
    {
        return $this->rfc->inNamespace();
    }

    /**
     * @return bool
     */
    public function isAbstract()
    {
        return $this->rfc->isAbstract();
    }

    /**
     * @return bool
     */
    public function isFinal()
    {
        return $this->rfc->isFinal();
    }
}
