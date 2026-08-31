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
use Monolog\Formatter\HtmlFormatter;
use Monolog\LogRecord;

/**
 * Base class for all mail handlers
 *
 * @author Gyula Sallai
 */
abstract class MailHandler extends AbstractProcessingHandler
{
    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        $messages = [];

        foreach ($records as $record) {
            if ($record->level->isLowerThan($this->level)) {
                continue;
            }

            $message = $this->processRecord($record);
            $messages[] = $message;
        }

        if (\count($messages) > 0) {
            $this->send((string) $this->getFormatter()->formatBatch($messages), $messages);
        }
    }

    /**
     * Send a mail with the given content
     *
     * @param string $content formatted email body to be sent
     * @param array  $records the array of log records that formed this content
     *
     * @phpstan-param non-empty-array<LogRecord> $records
     */
    abstract protected function send(string $content, array $records): void;

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->send((string) $record->formatted, [$record]);
    }

    /**
     * @phpstan-param non-empty-array<LogRecord> $records
     */
    protected function getHighestRecord(array $records): LogRecord
    {
        $highestRecord = null;
        foreach ($records as $record) {
            if ($highestRecord === null || $record->level->isHigherThan($highestRecord->level)) {
                $highestRecord = $record;
            }
        }

        return $highestRecord;
    }

    protected function isHtmlBody(string $body): bool
    {
        return ($body[0] ?? null) === '<';
    }

    /**
     * Gets the default formatter.
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new HtmlFormatter();
    }
}
