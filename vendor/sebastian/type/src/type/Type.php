<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

use function gettype;
use function is_object;
use function strtolower;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
abstract class Type
{
    public static function fromValue(mixed $value, bool $allowsNull): self
    {
        if ($allowsNull === false) {
            if ($value === true) {
                return new TrueType;
            }

            if ($value === false) {
                return new FalseType;
            }
        }

        $typeName = gettype($value);

        if (is_object($value)) {
            return new ObjectType(TypeName::fromQualifiedName($value::class), $allowsNull);
        }

        $type = self::fromName($typeName, $allowsNull);

        if ($type instanceof SimpleType) {
            $type = new SimpleType($typeName, $allowsNull, $value);
        }

        return $type;
    }

    /**
     * @param non-empty-string $typeName
     */
    public static function fromName(string $typeName, bool $allowsNull): self
    {
        return match (strtolower($typeName)) {
            'callable'     => new CallableType($allowsNull),
            'true'         => new TrueType,
            'false'        => new FalseType,
            'iterable'     => new IterableType($allowsNull),
            'never'        => new NeverType,
            'null'         => new NullType,
            'object'       => new GenericObjectType($allowsNull),
            'unknown type' => new UnknownType,
            'void'         => new VoidType,
            'array', 'bool', 'boolean', 'double', 'float', 'int', 'integer', 'real', 'resource', 'resource (closed)', 'string' => new SimpleType($typeName, $allowsNull),
            'mixed' => new MixedType,
            /** @phpstan-ignore argument.type */
            default => new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull),
        };
    }

    public function asString(): string
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }

    /**
     * @phpstan-assert-if-true CallableType $this
     */
    public function isCallable(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true TrueType $this
     */
    public function isTrue(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true FalseType $this
     */
    public function isFalse(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true GenericObjectType $this
     */
    public function isGenericObject(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true IntersectionType $this
     */
    public function isIntersection(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true IterableType $this
     */
    public function isIterable(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true MixedType $this
     */
    public function isMixed(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true NeverType $this
     */
    public function isNever(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true NullType $this
     */
    public function isNull(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true ObjectType $this
     */
    public function isObject(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true SimpleType $this
     */
    public function isSimple(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true StaticType $this
     */
    public function isStatic(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true UnionType $this
     */
    public function isUnion(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true UnknownType $this
     */
    public function isUnknown(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true VoidType $this
     */
    public function isVoid(): bool
    {
        return false;
    }

    abstract public function isAssignable(self $other): bool;

    /**
     * @return non-empty-string
     */
    abstract public function name(): string;

    abstract public function allowsNull(): bool;
}
