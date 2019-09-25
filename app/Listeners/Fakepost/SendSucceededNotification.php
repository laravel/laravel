<?php

namespace App\Listeners\Fakepost;

use App\Events\Fakepost\Succeeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;

class SendSucceededNotification
{
    /**
     * Handle the event.
     *
     * @param  Succeeded  $event
     * @return void
     */
    public function handle(Succeeded $event)
    {
        Log::notice("here handle SendSucceededFakepostNotification {$event->data}");
    }
}
