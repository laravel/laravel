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

use Monolog\Level;
use Monolog\ResettableInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Buffers all records until closing the handler and then pass them as batch.
 *
 * This is useful for a MailHandler to send only one mail per request instead of
 * sending one per log message.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class BufferHandler extends AbstractHandler implements ProcessableHandlerInterface, FormattableHandlerInterface
{
    use ProcessableHandlerTrait;

    protected HandlerInterface $handler;

    protected int $bufferSize = 0;

    protected int $bufferLimit;

    protected bool $flushOnOverflow;

    /** @var LogRecord[] */
    protected array $buffer = [];

    protected bool $initialized = false;

    /**
     * @param HandlerInterface $handler         Handler.
     * @param int              $bufferLimit     How many entries should be buffered at most, beyond that the oldest items are removed from the buffer.
     * @param bool             $flushOnOverflow If true, the buffer is flushed when the max size has been reached, by default oldest entries are discarded
     */
    public function __construct(HandlerInterface $handler, int $bufferLimit = 0, int|string|Level $level = Level::Debug, bool $bubble = true, bool $flushOnOverflow = false)
    {
        parent::__construct($level, $bubble);
        $this->handler = $handler;
        $this->bufferLimit = $bufferLimit;
        $this->flushOnOverflow = $flushOnOverflow;
    }

    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        if ($record->level->isLowerThan($this->level)) {
            return false;
        }

        if (!$this->initialized) {
            // __destructor() doesn't get called on Fatal errors
            register_shutdown_function([$this, 'close']);
            $this->initialized = true;
        }

        if ($this->bufferLimit > 0 && $this->bufferSize === $this->bufferLimit) {
            if ($this->flushOnOverflow) {
                $this->flush();
            } else {
                array_shift($this->buffer);
                $this->bufferSize--;
            }
        }

        if (\count($this->processors) > 0) {
            $record = $this->processRecord($record);
        }

        $this->buffer[] = $record;
        $this->bufferSize++;

        return false === $this->bubble;
    }

    public function flush(): void
    {
        if ($this->bufferSize === 0) {
            return;
        }

        $this->handler->handleBatch($this->buffer);
        $this->clear();
    }

    public function __destruct()
    {
        // suppress the parent behavior since we already have register_shutdown_function()
        // to call close(), and the reference contained there will prevent this from being
        // GC'd until the end of the request
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->flush();

        $this->handler->close();
    }

    /**
     * Clears the buffer without flushing any messages down to the wrapped handler.
     */
    public function clear(): void
    {
        $this->bufferSize = 0;
        $this->buffer = [];
    }

    public function reset(): void
    {
        $this->flush();

        parent::reset();

        $this->resetProcessors();

        if ($this->handler instanceof ResettableInterface) {
            $this->handler->reset();
        }
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

        throw new \UnexpectedValueException('The nested handler of type '.\get_class($this->handler).' does not support formatters.');
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): FormatterInterface
    {
        if ($this->handler instanceof FormattableHandlerInterface) {
            return $this->handler->getFormatter();
        }

        throw new \UnexpectedValueException('The nested handler of type '.\get_class($this->handler).' does not support formatters.');
    }

    public function setHandler(HandlerInterface $handler): void
    {
        $this->handler = $handler;
    }
}
