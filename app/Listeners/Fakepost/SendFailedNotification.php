<?php

namespace App\Listeners\Fakepost;

use App\Events\Fakepost\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;

class SendFailedNotification
{
    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $message = $event->getMessage();
        Log::critical("here handle SendFailedFakepostNotification {$message}");
    }
}
