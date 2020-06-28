<?php

namespace App\Listeners;

use Illuminate\Log\Logger;
use App\Dommain\BelongsToError;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogAnErrorListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BelongsToError  $event
     * @return void
     */
    public function handle($event)
    {
        Log::error($event->getError());
    }
}
