<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Listeners;

use Lightit\Shared\App\Events\TestEvent;

class TestListener
{
    /**
     * Handle the event.
     */
    public function handle(TestEvent $event): void
    {
    }
}
