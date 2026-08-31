<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Attribute\Reflection;

/**
 * @internal
 */
class ReflectionMember
{
    public function __construct(
        private readonly \ReflectionParameter|\ReflectionProperty $member,
    ) {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function getAttribute(string $class): ?object
    {
        return ($this->member->getAttributes($class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null)?->newInstance();
    }

    public function getSourceName(): string
    {
        if ($this->member instanceof \ReflectionProperty) {
            return $this->member->getDeclaringClass()->name;
        }

        $function = $this->member->getDeclaringFunction();

        if ($function instanceof \ReflectionMethod) {
            return $function->class.'::'.$function->name.'()';
        }

        return $function->name.'()';
    }

    public function getSourceThis(): ?object
    {
        if ($this->member instanceof \ReflectionParameter) {
            return $this->member->getDeclaringFunction()->getClosureThis();
        }

        return null;
    }

    public function getType(): ?\ReflectionType
    {
        return $this->member->getType();
    }

    public function getName(): string
    {
        return $this->member->getName();
    }

    public function hasDefaultValue(): bool
    {
        if ($this->member instanceof \ReflectionParameter) {
            return $this->member->isDefaultValueAvailable();
        }

        return $this->member->hasDefaultValue();
    }

    public function getDefaultValue(): mixed
    {
        $defaultValue = $this->member->getDefaultValue();

        if ($defaultValue instanceof \BackedEnum) {
            return $defaultValue->value;
        }

        return $defaultValue;
    }

    public function isNullable(): bool
    {
        return (bool) $this->member->getType()?->allowsNull();
    }

    public function getMemberName(): string
    {
        return $this->member instanceof \ReflectionParameter ? 'parameter' : 'property';
    }

    public function isParameter(): bool
    {
        return $this->member instanceof \ReflectionParameter;
    }

    public function isProperty(): bool
    {
        return $this->member instanceof \ReflectionProperty;
    }
}
