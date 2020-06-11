<?php

namespace ProjectName\Util;

trait DispatchEvents
{
    private function dispatchEvents(RaisesEvents $events): void
    {
        foreach ($events->release() as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
