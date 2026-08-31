<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Test;

use const PHP_EOL;
use function implode;
use function sprintf;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ErrorTriggered implements Event
{
    private Telemetry\Info $telemetryInfo;
    private Test $test;

    /**
     * @var non-empty-string
     */
    private string $message;

    /**
     * @var non-empty-string
     */
    private string $file;

    /**
     * @var positive-int
     */
    private int $line;
    private bool $suppressed;

    /**
     * @param non-empty-string $message
     * @param non-empty-string $file
     * @param positive-int     $line
     */
    public function __construct(Telemetry\Info $telemetryInfo, Test $test, string $message, string $file, int $line, bool $suppressed)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->test          = $test;
        $this->message       = $message;
        $this->file          = $file;
        $this->line          = $line;
        $this->suppressed    = $suppressed;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function test(): Test
    {
        return $this->test;
    }

    /**
     * @return non-empty-string
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @return non-empty-string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * @return positive-int
     */
    public function line(): int
    {
        return $this->line;
    }

    public function wasSuppressed(): bool
    {
        return $this->suppressed;
    }

    public function asString(): string
    {
        $message = $this->message;

        if (!empty($message)) {
            $message = PHP_EOL . $message;
        }

        $details = [$this->test->id()];

        if ($this->suppressed) {
            $details[] = 'suppressed using operator';
        }

        return sprintf(
            'Test Triggered Error (%s) in %s:%d%s',
            implode(', ', $details),
            $this->file,
            $this->line,
            $message,
        );
    }
}
