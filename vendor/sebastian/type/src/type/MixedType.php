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
final class MixedType extends Type
{
    public function isAssignable(Type $other): bool
    {
        return !$other instanceof VoidType;
    }

    /**
     * @return 'mixed'
     */
    public function asString(): string
    {
        return 'mixed';
    }

    /**
     * @return 'mixed'
     */
    public function name(): string
    {
        return 'mixed';
    }

    public function allowsNull(): bool
    {
        return true;
    }

    public function isMixed(): bool
    {
        return true;
    }
}
