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

use Monolog\Formatter\FormatterInterface;
use Monolog\ResettableInterface;
use Monolog\LogRecord;

/**
 * Forwards records to multiple handlers
 *
 * @author Lenar Lõhmus <lenar@city.ee>
 */
class GroupHandler extends Handler implements ProcessableHandlerInterface, ResettableInterface
{
    use ProcessableHandlerTrait;

    /** @var HandlerInterface[] */
    protected array $handlers;
    protected bool $bubble;

    /**
     * @param HandlerInterface[] $handlers Array of Handlers.
     * @param bool               $bubble   Whether the messages that are handled can bubble up the stack or not
     *
     * @throws \InvalidArgumentException if an unsupported handler is set
     */
    public function __construct(array $handlers, bool $bubble = true)
    {
        foreach ($handlers as $handler) {
            if (!$handler instanceof HandlerInterface) {
                throw new \InvalidArgumentException('The first argument of the GroupHandler must be an array of HandlerInterface instances.');
            }
        }

        $this->handlers = $handlers;
        $this->bubble = $bubble;
    }

    /**
     * @inheritDoc
     */
    public function isHandling(LogRecord $record): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->isHandling($record)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        if (\count($this->processors) > 0) {
            $record = $this->processRecord($record);
        }

        foreach ($this->handlers as $handler) {
            $handler->handle(clone $record);
        }

        return false === $this->bubble;
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        if (\count($this->processors) > 0) {
            $processed = [];
            foreach ($records as $record) {
                $processed[] = $this->processRecord($record);
            }
            $records = $processed;
        }

        foreach ($this->handlers as $handler) {
            $handler->handleBatch(array_map(fn ($record) => clone $record, $records));
        }
    }

    public function reset(): void
    {
        $this->resetProcessors();

        foreach ($this->handlers as $handler) {
            if ($handler instanceof ResettableInterface) {
                $handler->reset();
            }
        }
    }

    public function close(): void
    {
        parent::close();

        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($formatter);
            }
        }

        return $this;
    }
}
