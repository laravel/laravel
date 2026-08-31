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

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class FalseType extends Type
{
    public function isAssignable(Type $other): bool
    {
        if ($other instanceof self) {
            return true;
        }

        return $other instanceof SimpleType &&
              $other->name() === 'bool' &&
              $other->value() === false;
    }

    /**
     * @return 'false'
     */
    public function name(): string
    {
        return 'false';
    }

    public function allowsNull(): bool
    {
        return false;
    }

    public function isFalse(): bool
    {
        return true;
    }
}
