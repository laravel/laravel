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

use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Configured implements Event
{
    private Telemetry\Info $telemetryInfo;
    private Configuration $configuration;

    public function __construct(Telemetry\Info $telemetryInfo, Configuration $configuration)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->configuration = $configuration;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    public function asString(): string
    {
        return 'Test Runner Configured';
    }
}
