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
 * A fake ReflectionProperty for magic properties declared via @property docblock tags.
 *
 * This allows magic properties to be treated uniformly with real properties throughout
 * PsySH, including in SignatureFormatter, the ls command, and tab completion.
 *
 * Note: This implements \Reflector but does not extend \ReflectionProperty because
 * PHP's internal reflection classes have read-only properties that cannot be set.
 */
class ReflectionMagicProperty implements \Reflector
{
    private \ReflectionClass $declaringClass;
    public string $name;
    public string $class;
    private ?string $type;
    private bool $readOnly;
    private bool $writeOnly;
    private ?string $description;

    /**
     * Construct a ReflectionMagicProperty.
     *
     * @param \ReflectionClass $declaringClass The class that declares this magic property
     * @param string           $name           The property name (without $)
     * @param string|null      $type           The property type (from docblock)
     * @param bool             $readOnly       Whether this is a read-only property (@property)
     * @param bool             $writeOnly      Whether this is a write-only property (@property)
     * @param string|null      $description    The property description
     */
    public function __construct(
        \ReflectionClass $declaringClass,
        string $name,
        ?string $type = null,
        bool $readOnly = false,
        bool $writeOnly = false,
        ?string $description = null
    ) {
        $this->declaringClass = $declaringClass;
        $this->name = $name;
        $this->class = $declaringClass->getName();
        $this->type = $type;
        $this->readOnly = $readOnly;
        $this->writeOnly = $writeOnly;
        $this->description = $description;
    }

    /**
     * Get the property name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the class that declares this property.
     */
    public function getDeclaringClass(): \ReflectionClass
    {
        return $this->declaringClass;
    }

    /**
     * Magic properties are always public.
     */
    public function isPublic(): bool
    {
        return true;
    }

    /**
     * Magic properties are never protected.
     */
    public function isProtected(): bool
    {
        return false;
    }

    /**
     * Magic properties are never private.
     */
    public function isPrivate(): bool
    {
        return false;
    }

    /**
     * Magic properties are never static.
     */
    public function isStatic(): bool
    {
        return false;
    }

    /**
     * Check if this is a read-only property (@property).
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * Check if this is a write-only property (@property).
     */
    public function isWriteOnly(): bool
    {
        return $this->writeOnly;
    }

    /**
     * Magic properties don't have default values.
     */
    public function isDefault(): bool
    {
        return false;
    }

    /**
     * Get the property modifiers.
     */
    public function getModifiers(): int
    {
        return \ReflectionProperty::IS_PUBLIC;
    }

    /**
     * Get the docblock type for this property.
     */
    public function getDocblockType(): ?string
    {
        return $this->type;
    }

    /**
     * Get the description from the docblock.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Magic properties don't have a native type.
     */
    public function hasType(): bool
    {
        return false;
    }

    /**
     * Magic properties don't have a native type.
     */
    public function getType(): ?\ReflectionType
    {
        return null;
    }

    /**
     * Get the docblock for this magic property.
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
        throw new \RuntimeException('Export is not supported for magic properties');
    }

    /**
     * String representation.
     */
    public function __toString(): string
    {
        $type = $this->type ? $this->type.' ' : '';
        $suffix = '';

        if ($this->readOnly) {
            $suffix = ' (read-only)';
        } elseif ($this->writeOnly) {
            $suffix = ' (write-only)';
        }

        return \sprintf(
            'Property [ <magic> public %s$%s ]%s',
            $type,
            $this->name,
            $suffix
        );
    }
}
