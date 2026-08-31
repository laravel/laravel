<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Messenger;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RunCommandContext
{
    public function __construct(
        public readonly RunCommandMessage $message,
        public readonly int $exitCode,
        public readonly string $output,
    ) {
    }
}
