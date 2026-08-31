<?php declare(strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeUnit;

/**
 * @immutable
 */
final readonly class InterfaceMethodUnit extends CodeUnit
{
    public function isInterfaceMethod(): bool
    {
        return true;
    }
}
