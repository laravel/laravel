<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Formatter;

use Monolog\Level;
use Monolog\LogRecord;

/**
 * Formats a log message according to the ChromePHP array format
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChromePHPFormatter implements FormatterInterface
{
    /**
     * Translates Monolog log levels to Wildfire levels.
     *
     * @return 'log'|'info'|'warn'|'error'
     */
    private function toWildfireLevel(Level $level): string
    {
        return match ($level) {
            Level::Debug     => 'log',
            Level::Info      => 'info',
            Level::Notice    => 'info',
            Level::Warning   => 'warn',
            Level::Error     => 'error',
            Level::Critical  => 'error',
            Level::Alert     => 'error',
            Level::Emergency => 'error',
        };
    }

    /**
     * @inheritDoc
     */
    public function format(LogRecord $record)
    {
        // Retrieve the line and file if set and remove them from the formatted extra
        $backtrace = 'unknown';
        if (isset($record->extra['file'], $record->extra['line'])) {
            $backtrace = $record->extra['file'].' : '.$record->extra['line'];
            unset($record->extra['file'], $record->extra['line']);
        }

        $message = ['message' => $record->message];
        if (\count($record->context) > 0) {
            $message['context'] = $record->context;
        }
        if (\count($record->extra) > 0) {
            $message['extra'] = $record->extra;
        }
        if (\count($message) === 1) {
            $message = reset($message);
        }

        return [
            $record->channel,
            $message,
            $backtrace,
            $this->toWildfireLevel($record->level),
        ];
    }

    /**
     * @inheritDoc
     */
    public function formatBatch(array $records)
    {
        $formatted = [];

        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }

        return $formatted;
    }
}
