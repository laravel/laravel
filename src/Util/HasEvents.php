<?php

namespace ProjectName\Util;

use Illuminate\Contracts\Events\Dispatcher;

trait HasEvents
{
    protected array $events = [];

    public function release(): array
    {
        return $this->events;
    }

    public function dispatch(Dispatcher $dispatcher): void
    {
        while (count($this->events) > 0) {
            $event = array_shift($this->events);

            $dispatcher->dispatch($event);
        }
    }

    /**
     * Receive an event and add it to the events FIFO.
     * If the event was registered before it's replaced by the new one.
     */
    protected function raise(object $event): void
    {
        $this->events[get_class($event)] = $event;
    }

    protected function raiseMultiple(array $events): void
    {
        $this->events = array_merge($this->events, $events);
    }
}
