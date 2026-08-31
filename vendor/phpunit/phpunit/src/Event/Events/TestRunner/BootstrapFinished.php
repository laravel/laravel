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

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class BootstrapFinished implements Event
{
    private Telemetry\Info $telemetryInfo;
    private string $filename;

    public function __construct(Telemetry\Info $telemetryInfo, string $filename)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->filename      = $filename;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function asString(): string
    {
        return sprintf(
            'Bootstrap Finished (%s)',
            $this->filename,
        );
    }
}
