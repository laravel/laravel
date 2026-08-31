<?php

declare(strict_types=1);

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\ReportablePrinter;

/**
 * @internal
 */
abstract class Subscriber
{
    /**
     * The printer instance.
     */
    private ReportablePrinter $printer;

    /**
     * Creates a new subscriber.
     */
    public function __construct(ReportablePrinter $printer)
    {
        $this->printer = $printer;
    }

    /**
     * Returns the printer instance.
     */
    protected function printer(): ReportablePrinter
    {
        return $this->printer;
    }
}
