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
use function count;
use function implode;
use function sort;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class UnionType extends Type
{
    /**
     * @var non-empty-list<Type>
     */
    private array $types;

    /**
     * @throws RuntimeException
     */
    public function __construct(Type ...$types)
    {
        $this->ensureMinimumOfTwoTypes(...$types);
        $this->ensureOnlyValidTypes(...$types);

        assert(!empty($types));

        $this->types = $types;
    }

    public function isAssignable(Type $other): bool
    {
        foreach ($this->types as $type) {
            if ($type->isAssignable($other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return $this->name();
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        $types = [];

        foreach ($this->types as $type) {
            if ($type->isIntersection()) {
                $types[] = '(' . $type->name() . ')';

                continue;
            }

            $types[] = $type->name();
        }

        sort($types);

        return implode('|', $types);
    }

    public function allowsNull(): bool
    {
        foreach ($this->types as $type) {
            if ($type instanceof NullType) {
                return true;
            }
        }

        return false;
    }

    public function isUnion(): bool
    {
        return true;
    }

    public function containsIntersectionTypes(): bool
    {
        foreach ($this->types as $type) {
            if ($type->isIntersection()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return non-empty-list<Type>
     */
    public function types(): array
    {
        return $this->types;
    }

    /**
     * @throws RuntimeException
     */
    private function ensureMinimumOfTwoTypes(Type ...$types): void
    {
        if (count($types) < 2) {
            throw new RuntimeException(
                'A union type must be composed of at least two types',
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureOnlyValidTypes(Type ...$types): void
    {
        foreach ($types as $type) {
            if ($type instanceof UnknownType) {
                throw new RuntimeException(
                    'A union type must not be composed of an unknown type',
                );
            }

            if ($type instanceof VoidType) {
                throw new RuntimeException(
                    'A union type must not be composed of a void type',
                );
            }
        }
    }
}
