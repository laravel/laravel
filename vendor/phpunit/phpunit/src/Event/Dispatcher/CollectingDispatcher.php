<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event;

use PHPUnit\Runner\DeprecationCollector\Facade as DeprecationCollector;
use PHPUnit\Runner\DeprecationCollector\TestTriggeredDeprecationSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CollectingDispatcher implements Dispatcher
{
    private EventCollection $events;
    private DirectDispatcher $isolatedDirectDispatcher;

    public function __construct(DirectDispatcher $directDispatcher)
    {
        $this->isolatedDirectDispatcher = $directDispatcher;
        $this->events                   = new EventCollection;

        $this->isolatedDirectDispatcher->registerSubscriber(new TestTriggeredDeprecationSubscriber(DeprecationCollector::collector()));
    }

    public function dispatch(Event $event): void
    {
        $this->events->add($event);

        try {
            $this->isolatedDirectDispatcher->dispatch($event);
        } catch (UnknownEventTypeException) {
            // Do nothing.
        }
    }

    public function flush(): EventCollection
    {
        $events = $this->events;

        $this->events = new EventCollection;

        return $events;
    }
}
