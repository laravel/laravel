<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\TestRunner;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;
use PHPUnit\Event\TestSuite\TestSuite;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class ExecutionStarted implements Event
{
    private Telemetry\Info $telemetryInfo;
    private TestSuite $testSuite;

    public function __construct(Telemetry\Info $telemetryInfo, TestSuite $testSuite)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->testSuite     = $testSuite;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function testSuite(): TestSuite
    {
        return $this->testSuite;
    }

    public function asString(): string
    {
        return sprintf(
            'Test Runner Execution Started (%d test%s)',
            $this->testSuite->count(),
            $this->testSuite->count() !== 1 ? 's' : '',
        );
    }
}
