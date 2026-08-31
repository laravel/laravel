<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Telemetry;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class System
{
    private StopWatch $stopWatch;
    private MemoryMeter $memoryMeter;
    private GarbageCollectorStatusProvider $garbageCollectorStatusProvider;

    public function __construct(StopWatch $stopWatch, MemoryMeter $memoryMeter, GarbageCollectorStatusProvider $garbageCollectorStatusProvider)
    {
        $this->stopWatch                      = $stopWatch;
        $this->memoryMeter                    = $memoryMeter;
        $this->garbageCollectorStatusProvider = $garbageCollectorStatusProvider;
    }

    public function snapshot(): Snapshot
    {
        return new Snapshot(
            $this->stopWatch->current(),
            $this->memoryMeter->memoryUsage(),
            $this->memoryMeter->peakMemoryUsage(),
            $this->garbageCollectorStatusProvider->status(),
        );
    }
}
