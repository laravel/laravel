<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

/**
 * Contains all events dispatched by an Application.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
final class ConsoleEvents
{
    /**
     * The COMMAND event allows you to attach listeners before any command is
     * executed by the console. It also allows you to modify the command, input and output
     * before they are handled to the command.
     *
     * The event listener method receives a Symfony\Component\Console\Event\ConsoleCommandEvent
     * instance.
     *
     * @var string
     */
    const COMMAND = 'console.command';

    /**
     * The TERMINATE event allows you to attach listeners after a command is
     * executed by the console.
     *
     * The event listener method receives a Symfony\Component\Console\Event\ConsoleTerminateEvent
     * instance.
     *
     * @var string
     */
    const TERMINATE = 'console.terminate';

    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to deal with the exception or
     * to modify the thrown exception. The event listener method receives
     * a Symfony\Component\Console\Event\ConsoleExceptionEvent
     * instance.
     *
     * @var string
     */
    const EXCEPTION = 'console.exception';
}
