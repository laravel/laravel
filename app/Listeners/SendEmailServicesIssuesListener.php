<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Dommain\BelongsToResponse;

class SendEmailServicesIssuesListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Handle the event.
     *
     * @param  BelongsToResponse  $event
     * @return void
     */
    public function handle($event)
    {
        // here should send a email or a notification abut issue
        Log::error($event->getResponse()->getBody()->getContents());
    }
}
