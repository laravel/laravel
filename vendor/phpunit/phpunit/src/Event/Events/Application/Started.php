<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Application;

use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Runtime\Runtime;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Started implements Event
{
    private Telemetry\Info $telemetryInfo;
    private Runtime $runtime;

    public function __construct(Telemetry\Info $telemetryInfo, Runtime $runtime)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->runtime       = $runtime;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function runtime(): Runtime
    {
        return $this->runtime;
    }

    public function asString(): string
    {
        return sprintf(
            'PHPUnit Started (%s)',
            $this->runtime->asString(),
        );
    }
}
