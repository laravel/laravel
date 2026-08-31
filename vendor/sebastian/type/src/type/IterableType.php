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

use function assert;
use function class_exists;
use function is_iterable;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class IterableType extends Type
{
    private bool $allowsNull;

    public function __construct(bool $nullable)
    {
        $this->allowsNull = $nullable;
    }

    /**
     * @throws RuntimeException
     */
    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            return true;
        }

        if ($other instanceof SimpleType) {
            return is_iterable($other->value());
        }

        if ($other instanceof ObjectType) {
            $className = $other->className()->qualifiedName();

            assert(class_exists($className));

            return (new ReflectionClass($className))->isIterable();
        }

        return false;
    }

    /**
     * @return 'iterable'
     */
    public function name(): string
    {
        return 'iterable';
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isIterable(): bool
    {
        return true;
    }
}
