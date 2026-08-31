<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Reflection;

/**
 * A fake ReflectionMethod for magic methods declared via @method docblock tags.
 *
 * This allows magic methods to be treated uniformly with real methods throughout
 * PsySH, including in SignatureFormatter, the ls command, and tab completion.
 *
 * Note: This implements \Reflector but does not extend \ReflectionMethod because
 * PHP's internal reflection classes have read-only properties that cannot be set.
 */
class ReflectionMagicMethod implements \Reflector
{
    private \ReflectionClass $declaringClass;
    public string $name;
    public string $class;
    private bool $isStatic;
    private ?string $returnType;
    private string $parameters;
    private ?string $description;
    private bool $returnsReference;

    /**
     * Construct a ReflectionMagicMethod.
     *
     * @param \ReflectionClass $declaringClass   The class that declares this magic method
     * @param string           $name             The method name
     * @param bool             $isStatic         Whether this is a static method
     * @param string|null      $returnType       The return type (from docblock)
     * @param string           $parameters       The parameter string (without parentheses)
     * @param string|null      $description      The method description
     * @param bool             $returnsReference Whether the method returns by reference
     */
    public function __construct(
        \ReflectionClass $declaringClass,
        string $name,
        bool $isStatic = false,
        ?string $returnType = null,
        string $parameters = '',
        ?string $description = null,
        bool $returnsReference = false
    ) {
        $this->declaringClass = $declaringClass;
        $this->name = $name;
        $this->class = $declaringClass->getName();
        $this->isStatic = $isStatic;
        $this->returnType = $returnType;
        $this->parameters = $parameters;
        $this->description = $description;
        $this->returnsReference = $returnsReference;
    }

    /**
     * Get the method name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the class that declares this method.
     */
    public function getDeclaringClass(): \ReflectionClass
    {
        return $this->declaringClass;
    }

    /**
     * Check if this is a static method.
     */
    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    /**
     * Magic methods are always public.
     */
    public function isPublic(): bool
    {
        return true;
    }

    /**
     * Magic methods are never protected.
     */
    public function isProtected(): bool
    {
        return false;
    }

    /**
     * Magic methods are never private.
     */
    public function isPrivate(): bool
    {
        return false;
    }

    /**
     * Magic methods are never abstract.
     */
    public function isAbstract(): bool
    {
        return false;
    }

    /**
     * Magic methods are never final.
     */
    public function isFinal(): bool
    {
        return false;
    }

    /**
     * Check if this method returns by reference.
     */
    public function returnsReference(): bool
    {
        return $this->returnsReference;
    }

    /**
     * Get the method modifiers.
     */
    public function getModifiers(): int
    {
        $modifiers = \ReflectionMethod::IS_PUBLIC;

        if ($this->isStatic) {
            $modifiers |= \ReflectionMethod::IS_STATIC;
        }

        return $modifiers;
    }

    /**
     * Get parameters - returns empty array since we only have a string representation.
     *
     * @return \ReflectionParameter[]
     */
    public function getParameters(): array
    {
        return [];
    }

    /**
     * Get the raw parameter string from the docblock.
     */
    public function getParameterString(): string
    {
        return $this->parameters;
    }

    /**
     * Get the return type from the docblock.
     */
    public function getDocblockReturnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * Get the description from the docblock.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Magic methods don't have a native return type.
     */
    public function hasReturnType(): bool
    {
        return false;
    }

    /**
     * Magic methods don't have a native return type.
     */
    public function getReturnType(): ?\ReflectionType
    {
        return null;
    }

    /**
     * Get the docblock for this magic method.
     *
     * Returns the description if available.
     *
     * @return string|false
     */
    public function getDocComment()
    {
        if ($this->description === null) {
            return false;
        }

        return \sprintf("/**\n * %s\n */", $this->description);
    }

    /**
     * Export is not supported.
     *
     * @throws \RuntimeException
     */
    public static function export($class, $name, $return = false): ?string
    {
        throw new \RuntimeException('Export is not supported for magic methods');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        $static = $this->isStatic ? 'static ' : '';
        $return = $this->returnType ? $this->returnType.' ' : '';
        $ref = $this->returnsReference ? '&' : '';

        return \sprintf(
            'Method [ <magic> public %s%smethod %s%s ] { %s%s(%s) }',
            $static,
            $return,
            $ref,
            $this->name,
            $ref,
            $this->name,
            $this->parameters
        );
    }
}
