<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Test;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\JsonSerializableDateTimeImmutable;
use Monolog\Formatter\FormatterInterface;
use Psr\Log\LogLevel;

/**
 * Lets you easily generate log records and a dummy formatter for testing purposes
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class MonologTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array<mixed> $context
     * @param array<mixed> $extra
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    protected function getRecord(int|string|Level $level = Level::Warning, string|\Stringable $message = 'test', array $context = [], string $channel = 'test', \DateTimeImmutable $datetime = new JsonSerializableDateTimeImmutable(true), array $extra = []): LogRecord
    {
        return new LogRecord(
            message: (string) $message,
            context: $context,
            level: Logger::toMonologLevel($level),
            channel: $channel,
            datetime: $datetime,
            extra: $extra,
        );
    }

    /**
     * @phpstan-return list<LogRecord>
     */
    protected function getMultipleRecords(): array
    {
        return [
            $this->getRecord(Level::Debug, 'debug message 1'),
            $this->getRecord(Level::Debug, 'debug message 2'),
            $this->getRecord(Level::Info, 'information'),
            $this->getRecord(Level::Warning, 'warning'),
            $this->getRecord(Level::Error, 'error'),
        ];
    }

    protected function getIdentityFormatter(): FormatterInterface
    {
        $formatter = $this->createMock(FormatterInterface::class);
        $formatter->expects(self::any())
            ->method('format')
            ->willReturnCallback(function ($record) {
                return $record->message;
            });

        return $formatter;
    }
}
