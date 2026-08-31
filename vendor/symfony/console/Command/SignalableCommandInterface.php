<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

/**
 * Interface for command reacting to signal.
 *
 * @author Grégoire Pineau <lyrixx@lyrix.info>
 */
interface SignalableCommandInterface
{
    /**
     * Returns the list of signals to subscribe.
     *
     * @return list<\SIG*>
     *
     * @see https://php.net/pcntl.constants for signals
     */
    public function getSubscribedSignals(): array;

    /**
     * The method will be called when the application is signaled.
     *
     * @return int|false The exit code to return or false to continue the normal execution
     */
    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false;
}
