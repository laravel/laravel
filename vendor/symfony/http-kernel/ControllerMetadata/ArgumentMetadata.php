<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\ControllerMetadata;

/**
 * Responsible for storing metadata of an argument.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class ArgumentMetadata
{
    public const IS_INSTANCEOF = 2;

    /**
     * @param object[] $attributes
     */
    public function __construct(
        private string $name,
        private ?string $type,
        private bool $isVariadic,
        private bool $hasDefaultValue,
        private mixed $defaultValue,
        private bool $isNullable = false,
        private array $attributes = [],
        private string $controllerName = 'n/a',
    ) {
        $this->isNullable = $isNullable || null === $type || ($hasDefaultValue && null === $defaultValue);
    }

    /**
     * Returns the name as given in PHP, $foo would yield "foo".
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the type of the argument.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Returns whether the argument is defined as "...$variadic".
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }

    /**
     * Returns whether the argument has a default value.
     *
     * Implies whether an argument is optional.
     */
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * Returns whether the argument accepts null values.
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * Returns the default value of the argument.
     *
     * @throws \LogicException if no default value is present; {@see self::hasDefaultValue()}
     */
    public function getDefaultValue(): mixed
    {
        if (!$this->hasDefaultValue) {
            throw new \LogicException(\sprintf('Argument $%s does not have a default value. Use "%s::hasDefaultValue()" to avoid this exception.', $this->name, __CLASS__));
        }

        return $this->defaultValue;
    }

    /**
     * @param class-string          $name
     * @param self::IS_INSTANCEOF|0 $flags
     *
     * @return array<object>
     */
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        if (!$name) {
            return $this->attributes;
        }

        return $this->getAttributesOfType($name, $flags);
    }

    /**
     * @template T of object
     *
     * @param class-string<T>       $name
     * @param self::IS_INSTANCEOF|0 $flags
     *
     * @return array<T>
     */
    public function getAttributesOfType(string $name, int $flags = 0): array
    {
        $attributes = [];
        if ($flags & self::IS_INSTANCEOF) {
            foreach ($this->attributes as $attribute) {
                if ($attribute instanceof $name) {
                    $attributes[] = $attribute;
                }
            }
        } else {
            foreach ($this->attributes as $attribute) {
                if ($attribute::class === $name) {
                    $attributes[] = $attribute;
                }
            }
        }

        return $attributes;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }
}
