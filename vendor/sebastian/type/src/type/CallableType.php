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
use function count;
use function explode;
use function function_exists;
use function is_array;
use function is_object;
use function is_string;
use function str_contains;
use Closure;
use ReflectionClass;
use ReflectionObject;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class CallableType extends Type
{
    private bool $allowsNull;

    public function __construct(bool $nullable)
    {
        $this->allowsNull = $nullable;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            return true;
        }

        if ($other instanceof ObjectType) {
            if ($this->isClosure($other)) {
                return true;
            }

            if ($this->hasInvokeMethod($other)) {
                return true;
            }
        }

        if ($other instanceof SimpleType) {
            if ($this->isFunction($other)) {
                return true;
            }

            if ($this->isClassCallback($other)) {
                return true;
            }

            if ($this->isObjectCallback($other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return 'callable'
     */
    public function name(): string
    {
        return 'callable';
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function isCallable(): bool
    {
        return true;
    }

    private function isClosure(ObjectType $type): bool
    {
        return $type->className()->qualifiedName() === Closure::class;
    }

    private function hasInvokeMethod(ObjectType $type): bool
    {
        $className = $type->className()->qualifiedName();

        assert(class_exists($className));

        return (new ReflectionClass($className))->hasMethod('__invoke');
    }

    private function isFunction(SimpleType $type): bool
    {
        if (!is_string($type->value())) {
            return false;
        }

        return function_exists($type->value());
    }

    private function isObjectCallback(SimpleType $type): bool
    {
        if (!is_array($type->value())) {
            return false;
        }

        if (count($type->value()) !== 2) {
            return false;
        }

        if (!isset($type->value()[0], $type->value()[1])) {
            return false;
        }

        if (!is_object($type->value()[0]) || !is_string($type->value()[1])) {
            return false;
        }

        [$object, $methodName] = $type->value();

        return (new ReflectionObject($object))->hasMethod($methodName);
    }

    private function isClassCallback(SimpleType $type): bool
    {
        if (!is_string($type->value()) && !is_array($type->value())) {
            return false;
        }

        if (is_string($type->value())) {
            if (!str_contains($type->value(), '::')) {
                return false;
            }

            [$className, $methodName] = explode('::', $type->value());
        }

        if (is_array($type->value())) {
            if (count($type->value()) !== 2) {
                return false;
            }

            if (!isset($type->value()[0], $type->value()[1])) {
                return false;
            }

            if (!is_string($type->value()[0]) || !is_string($type->value()[1])) {
                return false;
            }

            [$className, $methodName] = $type->value();
        }

        if (!class_exists($className)) {
            return false;
        }

        $class = new ReflectionClass($className);

        if (!$class->hasMethod($methodName)) {
            return false;
        }

        $method = $class->getMethod($methodName);

        return $method->isPublic() && $method->isStatic();
    }
}
