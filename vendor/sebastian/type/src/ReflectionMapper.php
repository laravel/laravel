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

use function array_filter;
use function assert;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class ReflectionMapper
{
    /**
     * @return list<Parameter>
     */
    public function fromParameterTypes(ReflectionFunction|ReflectionMethod $reflector): array
    {
        $parameters = [];

        foreach ($reflector->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (!$parameter->hasType()) {
                $parameters[] = new Parameter($name, new UnknownType);

                continue;
            }

            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType) {
                $parameters[] = new Parameter(
                    $name,
                    $this->mapNamedType($type, $reflector),
                );

                continue;
            }

            if ($type instanceof ReflectionUnionType) {
                $parameters[] = new Parameter(
                    $name,
                    $this->mapUnionType($type, $reflector),
                );

                continue;
            }

            if ($type instanceof ReflectionIntersectionType) {
                $parameters[] = new Parameter(
                    $name,
                    $this->mapIntersectionType($type, $reflector),
                );
            }
        }

        return $parameters;
    }

    public function fromReturnType(ReflectionFunction|ReflectionMethod $reflector): Type
    {
        if (!$this->hasReturnType($reflector)) {
            return new UnknownType;
        }

        $returnType = $this->returnType($reflector);

        assert($returnType instanceof ReflectionNamedType || $returnType instanceof ReflectionUnionType || $returnType instanceof ReflectionIntersectionType);

        if ($returnType instanceof ReflectionNamedType) {
            return $this->mapNamedType($returnType, $reflector);
        }

        if ($returnType instanceof ReflectionUnionType) {
            return $this->mapUnionType($returnType, $reflector);
        }

        return $this->mapIntersectionType($returnType, $reflector);
    }

    public function fromPropertyType(ReflectionProperty $reflector): Type
    {
        if (!$reflector->hasType()) {
            return new UnknownType;
        }

        $propertyType = $reflector->getType();

        assert($propertyType instanceof ReflectionNamedType || $propertyType instanceof ReflectionUnionType || $propertyType instanceof ReflectionIntersectionType);

        if ($propertyType instanceof ReflectionNamedType) {
            return $this->mapNamedType($propertyType, $reflector);
        }

        if ($propertyType instanceof ReflectionUnionType) {
            return $this->mapUnionType($propertyType, $reflector);
        }

        return $this->mapIntersectionType($propertyType, $reflector);
    }

    private function mapNamedType(ReflectionNamedType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $classScope = !$reflector instanceof ReflectionFunction;
        $typeName   = $type->getName();

        assert($typeName !== '');

        if ($classScope && $typeName === 'self') {
            return ObjectType::fromName(
                $reflector->getDeclaringClass()->getName(),
                $type->allowsNull(),
            );
        }

        if ($classScope && $typeName === 'static') {
            return new StaticType(
                TypeName::fromReflection($reflector->getDeclaringClass()),
                $type->allowsNull(),
            );
        }

        if ($typeName === 'mixed') {
            return new MixedType;
        }

        if ($classScope && $typeName === 'parent') {
            $parentClass = $reflector->getDeclaringClass()->getParentClass();

            assert($parentClass !== false);

            return ObjectType::fromName(
                $parentClass->getName(),
                $type->allowsNull(),
            );
        }

        return Type::fromName(
            $typeName,
            $type->allowsNull(),
        );
    }

    private function mapUnionType(ReflectionUnionType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $types             = [];
        $objectType        = false;
        $genericObjectType = false;

        foreach ($type->getTypes() as $_type) {
            if ($_type instanceof ReflectionNamedType) {
                $namedType = $this->mapNamedType($_type, $reflector);

                if ($namedType instanceof GenericObjectType) {
                    $genericObjectType = true;
                } elseif ($namedType instanceof ObjectType) {
                    $objectType = true;
                }

                $types[] = $namedType;

                continue;
            }

            $types[] = $this->mapIntersectionType($_type, $reflector);
        }

        if ($objectType && $genericObjectType) {
            $types = array_filter(
                $types,
                static function (Type $type): bool
                {
                    if ($type instanceof ObjectType) {
                        return false;
                    }

                    return true;
                },
            );
        }

        return new UnionType(...$types);
    }

    private function mapIntersectionType(ReflectionIntersectionType $type, ReflectionFunction|ReflectionMethod|ReflectionProperty $reflector): Type
    {
        $types = [];

        foreach ($type->getTypes() as $_type) {
            assert($_type instanceof ReflectionNamedType);

            $types[] = $this->mapNamedType($_type, $reflector);
        }

        return new IntersectionType(...$types);
    }

    private function hasReturnType(ReflectionFunction|ReflectionMethod $reflector): bool
    {
        if ($reflector->hasReturnType()) {
            return true;
        }

        return $reflector->hasTentativeReturnType();
    }

    private function returnType(ReflectionFunction|ReflectionMethod $reflector): ?ReflectionType
    {
        if ($reflector->hasReturnType()) {
            return $reflector->getReturnType();
        }

        return $reflector->getTentativeReturnType();
    }
}
