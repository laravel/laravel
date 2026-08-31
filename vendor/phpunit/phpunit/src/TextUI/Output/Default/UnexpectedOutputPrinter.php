<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\Output\Default;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\PrintedUnexpectedOutputSubscriber;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TextUI\Output\Printer;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class UnexpectedOutputPrinter implements PrintedUnexpectedOutputSubscriber
{
    private Printer $printer;

    /**
     * @throws EventFacadeIsSealedException
     * @throws UnknownSubscriberTypeException
     */
    public function __construct(Printer $printer, Facade $facade)
    {
        $this->printer = $printer;

        $facade->registerSubscriber($this);
    }

    public function notify(PrintedUnexpectedOutput $event): void
    {
        $this->printer->print($event->output());
    }
}
