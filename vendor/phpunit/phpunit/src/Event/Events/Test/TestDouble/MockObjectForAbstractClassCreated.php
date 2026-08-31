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
use PHPUnit\Event\Event;
use PHPUnit\Event\Telemetry;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MockObjectForAbstractClassCreated implements Event
{
    private Telemetry\Info $telemetryInfo;

    /**
     * @var class-string
     */
    private string $className;

    /**
     * @param class-string $className
     */
    public function __construct(Telemetry\Info $telemetryInfo, string $className)
    {
        $this->telemetryInfo = $telemetryInfo;
        $this->className     = $className;
    }

    public function telemetryInfo(): Telemetry\Info
    {
        return $this->telemetryInfo;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    public function asString(): string
    {
        return sprintf(
            'Mock Object Created (%s)',
            $this->className,
        );
    }
}
