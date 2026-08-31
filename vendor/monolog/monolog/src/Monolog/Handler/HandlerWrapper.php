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

use Monolog\ResettableInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * This simple wrapper class can be used to extend handlers functionality.
 *
 * Example: A custom filtering that can be applied to any handler.
 *
 * Inherit from this class and override handle() like this:
 *
 *   public function handle(LogRecord $record)
 *   {
 *        if ($record meets certain conditions) {
 *            return false;
 *        }
 *        return $this->handler->handle($record);
 *   }
 *
 * @author Alexey Karapetov <alexey@karapetov.com>
 */
class HandlerWrapper implements HandlerInterface, ProcessableHandlerInterface, FormattableHandlerInterface, ResettableInterface
{
    protected HandlerInterface $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function isHandling(LogRecord $record): bool
    {
        return $this->handler->isHandling($record);
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        return $this->handler->handle($record);
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        $this->handler->handleBatch($records);
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->handler->close();
    }

    /**
     * @inheritDoc
     */
    public function pushProcessor(callable $callback): HandlerInterface
    {
        if ($this->handler instanceof ProcessableHandlerInterface) {
            $this->handler->pushProcessor($callback);

            return $this;
        }

        throw new \LogicException('The wrapped handler does not implement ' . ProcessableHandlerInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function popProcessor(): callable
    {
        if ($this->handler instanceof ProcessableHandlerInterface) {
            return $this->handler->popProcessor();
        }

        throw new \LogicException('The wrapped handler does not implement ' . ProcessableHandlerInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if ($this->handler instanceof FormattableHandlerInterface) {
            $this->handler->setFormatter($formatter);

            return $this;
        }

        throw new \LogicException('The wrapped handler does not implement ' . FormattableHandlerInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): FormatterInterface
    {
        if ($this->handler instanceof FormattableHandlerInterface) {
            return $this->handler->getFormatter();
        }

        throw new \LogicException('The wrapped handler does not implement ' . FormattableHandlerInterface::class);
    }

    public function reset(): void
    {
        if ($this->handler instanceof ResettableInterface) {
            $this->handler->reset();
        }
    }
}
