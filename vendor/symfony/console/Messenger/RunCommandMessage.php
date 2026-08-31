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

use Symfony\Component\Console\Exception\RunCommandFailedException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class RunCommandMessage implements \Stringable
{
    /**
     * @param bool $throwOnFailure  If the command has a non-zero exit code, throw {@see RunCommandFailedException}
     * @param bool $catchExceptions @see Application::setCatchExceptions()
     */
    public function __construct(
        public readonly string $input,
        public readonly bool $throwOnFailure = true,
        public readonly bool $catchExceptions = false,
    ) {
    }

    public function __toString(): string
    {
        return $this->input;
    }
}
