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

use function strtolower;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class SimpleType extends Type
{
    /**
     * @var non-empty-string
     */
    private string $name;
    private bool $allowsNull;
    private mixed $value;

    /**
     * @param non-empty-string $name
     */
    public function __construct(string $name, bool $nullable, mixed $value = null)
    {
        $this->name       = $this->normalize($name);
        $this->allowsNull = $nullable;
        $this->value      = $value;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($this->name === 'bool' && $other->name() === 'true') {
            return true;
        }

        if ($this->name === 'bool' && $other->name() === 'false') {
            return true;
        }

        if ($other instanceof self) {
            return $this->name === $other->name;
        }

        return false;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function isSimple(): bool
    {
        return true;
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    private function normalize(string $name): string
    {
        $name = strtolower($name);

        return match ($name) {
            'boolean' => 'bool',
            'real', 'double' => 'float',
            'integer' => 'int',
            '[]'      => 'array',
            default   => $name,
        };
    }
}
