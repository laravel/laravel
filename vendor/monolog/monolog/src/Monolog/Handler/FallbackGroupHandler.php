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

use Throwable;
use Monolog\LogRecord;

/**
 * Forwards records to at most one handler
 *
 * If a handler fails, the exception is suppressed and the record is forwarded to the next handler.
 *
 * As soon as one handler handles a record successfully, the handling stops there.
 */
class FallbackGroupHandler extends GroupHandler
{
    /**
     * @inheritDoc
     */
    public function handle(LogRecord $record): bool
    {
        if (\count($this->processors) > 0) {
            $record = $this->processRecord($record);
        }
        foreach ($this->handlers as $handler) {
            try {
                $handler->handle(clone $record);
                break;
            } catch (Throwable $e) {
                // What throwable?
            }
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
            try {
                $handler->handleBatch(array_map(fn ($record) => clone $record, $records));
                break;
            } catch (Throwable $e) {
                // What throwable?
            }
        }
    }
}
