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

use function sprintf;
use PHPUnit\Event\Code;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Finished implements Event
{
    private Telemetry\Info $telemetryInfo;
    private Code\Test $test;
    private int $numberOfAssertionsPerformed;

    public function __construct(Telemetry\Info $telemetryInfo, Code\Test $test, int $numberOfAssertionsPerformed)
    {
        $this->telemetryInfo               = $telemetryInfo;
        $this->test                        = $test;
        $this->numberOfAssertionsPerformed = $numberOfAssertionsPerformed;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function test(): Code\Test
    {
        return $this->test;
    }

    public function numberOfAssertionsPerformed(): int
    {
        return $this->numberOfAssertionsPerformed;
    }

    public function asString(): string
    {
        return sprintf(
            'Test Finished (%s)',
            $this->test->id(),
        );
    }
}
