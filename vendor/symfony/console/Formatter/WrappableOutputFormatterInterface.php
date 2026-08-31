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
 * Formatter interface for console output that supports word wrapping.
 *
 * @author Roland Franssen <franssen.roland@gmail.com>
 */
interface WrappableOutputFormatterInterface extends OutputFormatterInterface
{
    /**
     * Formats a message according to the given styles, wrapping at `$width` (0 means no wrapping).
     */
    public function formatAndWrap(?string $message, int $width): string;
}
