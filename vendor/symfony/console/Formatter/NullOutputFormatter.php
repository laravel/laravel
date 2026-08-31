<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Formatter;

/**
 * @author Tien Xuan Vo <tien.xuan.vo@gmail.com>
 */
final class NullOutputFormatter implements OutputFormatterInterface
{
    private NullOutputFormatterStyle $style;

    public function format(?string $message): ?string
    {
        return null;
    }

    public function getStyle(string $name): OutputFormatterStyleInterface
    {
        // to comply with the interface we must return a OutputFormatterStyleInterface
        return $this->style ??= new NullOutputFormatterStyle();
    }

    public function hasStyle(string $name): bool
    {
        return false;
    }

    public function isDecorated(): bool
    {
        return false;
    }

    public function setDecorated(bool $decorated): void
    {
        // do nothing
    }

    public function setStyle(string $name, OutputFormatterStyleInterface $style): void
    {
        // do nothing
    }
}
