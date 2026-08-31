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
use Rollbar\RollbarLogger;
use Throwable;
use Monolog\LogRecord;

/**
 * Sends errors to Rollbar
 *
 * If the context data contains a `payload` key, that is used as an array
 * of payload options to RollbarLogger's log method.
 *
 * Rollbar's context info will contain the context + extra keys from the log record
 * merged, and then on top of that a few keys:
 *
 *  - level (rollbar level name)
 *  - monolog_level (monolog level name, raw level, as rollbar only has 5 but monolog 8)
 *  - channel
 *  - datetime (unix timestamp)
 *
 * @author Paul Statezny <paulstatezny@gmail.com>
 */
class RollbarHandler extends AbstractProcessingHandler
{
    protected RollbarLogger $rollbarLogger;

    /**
     * Records whether any log records have been added since the last flush of the rollbar notifier
     */
    private bool $hasRecords = false;

    protected bool $initialized = false;

    /**
     * @param RollbarLogger $rollbarLogger RollbarLogger object constructed with valid token
     */
    public function __construct(RollbarLogger $rollbarLogger, int|string|Level $level = Level::Error, bool $bubble = true)
    {
        $this->rollbarLogger = $rollbarLogger;

        parent::__construct($level, $bubble);
    }

    /**
     * Translates Monolog log levels to Rollbar levels.
     *
     * @return 'debug'|'info'|'warning'|'error'|'critical'
     */
    protected function toRollbarLevel(Level $level): string
    {
        return match ($level) {
            Level::Debug     => 'debug',
            Level::Info      => 'info',
            Level::Notice    => 'info',
            Level::Warning   => 'warning',
            Level::Error     => 'error',
            Level::Critical  => 'critical',
            Level::Alert     => 'critical',
            Level::Emergency => 'critical',
        };
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        if (!$this->initialized) {
            // __destructor() doesn't get called on Fatal errors
            register_shutdown_function([$this, 'close']);
            $this->initialized = true;
        }

        $context = $record->context;
        $context = array_merge($context, $record->extra, [
            'level' => $this->toRollbarLevel($record->level),
            'monolog_level' => $record->level->getName(),
            'channel' => $record->channel,
            'datetime' => $record->datetime->format('U'),
        ]);

        if (isset($context['exception']) && $context['exception'] instanceof Throwable) {
            $exception = $context['exception'];
            unset($context['exception']);
            $toLog = $exception;
        } else {
            $toLog = $record->message;
        }

        $this->rollbarLogger->log($context['level'], $toLog, $context);

        $this->hasRecords = true;
    }

    public function flush(): void
    {
        if ($this->hasRecords) {
            $this->rollbarLogger->flush();
            $this->hasRecords = false;
        }
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->flush();
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        $this->flush();

        parent::reset();
    }
}
