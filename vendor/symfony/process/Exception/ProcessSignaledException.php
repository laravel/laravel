<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process\Exception;

use Symfony\Component\Process\Process;

/**
 * Exception that is thrown when a process has been signaled.
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class ProcessSignaledException extends RuntimeException
{
    public function __construct(
        private Process $process,
    ) {
        parent::__construct(\sprintf('The process has been signaled with signal "%s".', $process->getTermSignal()));
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getSignal(): int
    {
        return $this->getProcess()->getTermSignal();
    }
}
