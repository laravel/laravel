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

use function gc_status;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class Php83GarbageCollectorStatusProvider implements GarbageCollectorStatusProvider
{
    public function status(): GarbageCollectorStatus
    {
        $status = gc_status();

        return new GarbageCollectorStatus(
            $status['runs'],
            $status['collected'],
            $status['threshold'],
            $status['roots'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['application_time'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['collector_time'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['destructor_time'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['free_time'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['running'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['protected'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['full'],
            /** @phpstan-ignore offsetAccess.notFound */
            $status['buffer_size'],
        );
    }
}
