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
use Monolog\Logger;
use Psr\Log\LogLevel;
use Monolog\LogRecord;
use NoDiscard;

/**
 * Used for testing purposes.
 *
 * It records all records and gives you access to them for verification.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 *
 * @method bool hasEmergency(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasAlert(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasCritical(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasError(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasWarning(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasNotice(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasInfo(array{message: string, context?: mixed[]}|string $recordAssertions)
 * @method bool hasDebug(array{message: string, context?: mixed[]}|string $recordAssertions)
 *
 * @method bool hasEmergencyRecords()
 * @method bool hasAlertRecords()
 * @method bool hasCriticalRecords()
 * @method bool hasErrorRecords()
 * @method bool hasWarningRecords()
 * @method bool hasNoticeRecords()
 * @method bool hasInfoRecords()
 * @method bool hasDebugRecords()
 *
 * @method bool hasEmergencyThatContains(string $message)
 * @method bool hasAlertThatContains(string $message)
 * @method bool hasCriticalThatContains(string $message)
 * @method bool hasErrorThatContains(string $message)
 * @method bool hasWarningThatContains(string $message)
 * @method bool hasNoticeThatContains(string $message)
 * @method bool hasInfoThatContains(string $message)
 * @method bool hasDebugThatContains(string $message)
 *
 * @method bool hasEmergencyThatMatches(string $regex)
 * @method bool hasAlertThatMatches(string $regex)
 * @method bool hasCriticalThatMatches(string $regex)
 * @method bool hasErrorThatMatches(string $regex)
 * @method bool hasWarningThatMatches(string $regex)
 * @method bool hasNoticeThatMatches(string $regex)
 * @method bool hasInfoThatMatches(string $regex)
 * @method bool hasDebugThatMatches(string $regex)
 *
 * @method bool hasEmergencyThatPasses(callable $predicate)
 * @method bool hasAlertThatPasses(callable $predicate)
 * @method bool hasCriticalThatPasses(callable $predicate)
 * @method bool hasErrorThatPasses(callable $predicate)
 * @method bool hasWarningThatPasses(callable $predicate)
 * @method bool hasNoticeThatPasses(callable $predicate)
 * @method bool hasInfoThatPasses(callable $predicate)
 * @method bool hasDebugThatPasses(callable $predicate)
 */
class TestHandler extends AbstractProcessingHandler
{
    /** @var LogRecord[] */
    protected array $records = [];
    /** @phpstan-var array<value-of<Level::VALUES>, LogRecord[]> */
    protected array $recordsByLevel = [];
    private bool $skipReset = false;

    /**
     * @return array<LogRecord>
     */
    #[NoDiscard]
    public function getRecords(): array
    {
        return $this->records;
    }

    public function clear(): void
    {
        $this->records = [];
        $this->recordsByLevel = [];
    }

    public function reset(): void
    {
        if (!$this->skipReset) {
            $this->clear();
        }
    }

    public function setSkipReset(bool $skipReset): void
    {
        $this->skipReset = $skipReset;
    }

    /**
     * @param int|string|Level|LogLevel::* $level Logging level value or name
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    #[NoDiscard]
    public function hasRecords(int|string|Level $level): bool
    {
        return isset($this->recordsByLevel[Logger::toMonologLevel($level)->value]);
    }

    /**
     * @param string|array $recordAssertions Either a message string or an array containing message and optionally context keys that will be checked against all records
     *
     * @phpstan-param array{message: string, context?: mixed[]}|string $recordAssertions
     */
    #[NoDiscard]
    public function hasRecord(string|array $recordAssertions, Level $level): bool
    {
        if (\is_string($recordAssertions)) {
            $recordAssertions = ['message' => $recordAssertions];
        }

        return $this->hasRecordThatPasses(function (LogRecord $rec) use ($recordAssertions) {
            if ($rec->message !== $recordAssertions['message']) {
                return false;
            }
            if (isset($recordAssertions['context']) && $rec->context !== $recordAssertions['context']) {
                return false;
            }

            return true;
        }, $level);
    }

    #[NoDiscard]
    public function hasRecordThatContains(string $message, Level $level): bool
    {
        return $this->hasRecordThatPasses(fn (LogRecord $rec) => str_contains($rec->message, $message), $level);
    }

    #[NoDiscard]
    public function hasRecordThatMatches(string $regex, Level $level): bool
    {
        return $this->hasRecordThatPasses(fn (LogRecord $rec) => preg_match($regex, $rec->message) > 0, $level);
    }

    /**
     * @phpstan-param callable(LogRecord, int): mixed $predicate
     */
    #[NoDiscard]
    public function hasRecordThatPasses(callable $predicate, Level $level): bool
    {
        $level = Logger::toMonologLevel($level);

        if (!isset($this->recordsByLevel[$level->value])) {
            return false;
        }

        foreach ($this->recordsByLevel[$level->value] as $i => $rec) {
            if ((bool) $predicate($rec, $i)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->recordsByLevel[$record->level->value][] = $record;
        $this->records[] = $record;
    }

    /**
     * @param mixed[] $args
     */
    #[NoDiscard]
    public function __call(string $method, array $args): bool
    {
        if ((bool) preg_match('/(.*)(Debug|Info|Notice|Warning|Error|Critical|Alert|Emergency)(.*)/', $method, $matches)) {
            $genericMethod = $matches[1] . ('Records' !== $matches[3] ? 'Record' : '') . $matches[3];
            $level = \constant(Level::class.'::' . $matches[2]);
            $callback = [$this, $genericMethod];
            if (\is_callable($callback)) {
                $args[] = $level;

                return \call_user_func_array($callback, $args);
            }
        }

        throw new \BadMethodCallException('Call to undefined method ' . \get_class($this) . '::' . $method . '()');
    }
}
