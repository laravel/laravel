<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Printers;

use Throwable;

/**
 * @internal
 *
 * @mixin DefaultPrinter
 */
final class ReportablePrinter
{
    /**
     * Creates a new Printer instance.
     */
    public function __construct(private readonly DefaultPrinter $printer)
    {
        // ..
    }

    /**
     * Calls the original method, but reports any errors to the reporter.
     */
    public function __call(string $name, array $arguments): mixed
    {
        try {
            return $this->printer->$name(...$arguments);
        } catch (Throwable $throwable) {
            $this->printer->report($throwable);
        }

        exit(1);
    }
}
