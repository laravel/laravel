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
use function sprintf;
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class PrintedUnexpectedOutput implements Event
{
    private Telemetry\Info $telemetryInfo;

    /**
     * @var non-empty-string
     */
    private string $output;

    /**
     * @param non-empty-string $output
     */
    public function __construct(Telemetry\Info $telemetryInfo, string $output)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->output        = $output;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    /**
     * @return non-empty-string
     */
    public function output(): string
    {
        return $this->output;
    }

    public function asString(): string
    {
        return sprintf(
            'Test Printed Unexpected Output%s%s',
            PHP_EOL,
            $this->output,
        );
    }
}
