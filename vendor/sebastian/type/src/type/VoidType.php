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
final class VoidType extends Type
{
    public function isAssignable(Type $other): bool
    {
        return $other instanceof self;
    }

    /**
     * @return 'void'
     */
    public function name(): string
    {
        return 'void';
    }

    public function allowsNull(): bool
    {
        return false;
    }

    public function isVoid(): bool
    {
        return true;
    }
}
