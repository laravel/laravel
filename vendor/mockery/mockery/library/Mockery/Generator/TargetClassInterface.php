<?php

/**
 * Mockery (https://docs.mockery.io/)
 *
 * @copyright https://github.com/mockery/mockery/blob/HEAD/COPYRIGHT.md
 * @license https://github.com/mockery/mockery/blob/HEAD/LICENSE BSD 3-Clause License
 * @link https://github.com/mockery/mockery for the canonical source repository
 */

namespace Mockery\Generator;

interface TargetClassInterface
{
    /**
     * Returns a new instance of the current TargetClassInterface's implementation.
     *
     * @param class-string $name
     *
     * @return TargetClassInterface
     */
    public static function factory($name);

    /**
     * Returns the targetClass's attributes.
     *
     * @return array<class-string>
     */
    public function getAttributes();

    /**
     * Returns the targetClass's interfaces.
     *
     * @return array<TargetClassInterface>
     */
    public function getInterfaces();

    /**
     * Returns the targetClass's methods.
     *
     * @return array<Method>
     */
    public function getMethods();

    /**
     * Returns the targetClass's name.
     *
     * @return class-string
     */
    public function getName();

    /**
     * Returns the targetClass's namespace name.
     *
     * @return string
     */
    public function getNamespaceName();

    /**
     * Returns the targetClass's short name.
     *
     * @return string
     */
    public function getShortName();

    /**
     * Returns whether the targetClass has
     * an internal ancestor.
     *
     * @return bool
     */
    public function hasInternalAncestor();

    /**
     * Returns whether the targetClass is in
     * the passed interface.
     *
     * @param class-string|string $interface
     *
     * @return bool
     */
    public function implementsInterface($interface);

    /**
     * Returns whether the targetClass is in namespace.
     *
     * @return bool
     */
    public function inNamespace();

    /**
     * Returns whether the targetClass is abstract.
     *
     * @return bool
     */
    public function isAbstract();

    /**
     * Returns whether the targetClass is final.
     *
     * @return bool
     */
    public function isFinal();
}
