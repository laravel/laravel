<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Processor\ProcessorInterface;
use Monolog\LogRecord;

/**
 * Interface to describe loggers that have processors
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface ProcessableHandlerInterface
{
    /**
     * Adds a processor in the stack.
     *
     * @phpstan-param ProcessorInterface|(callable(LogRecord): LogRecord) $callback
     *
     * @param  ProcessorInterface|callable $callback
     * @return HandlerInterface            self
     */
    public function pushProcessor(callable $callback): HandlerInterface;

    /**
     * Removes the processor on top of the stack and returns it.
     *
     * @phpstan-return ProcessorInterface|(callable(LogRecord): LogRecord) $callback
     *
     * @throws \LogicException             In case the processor stack is empty
     * @return callable|ProcessorInterface
     */
    public function popProcessor(): callable;
}
