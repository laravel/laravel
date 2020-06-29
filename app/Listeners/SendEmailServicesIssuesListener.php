<?php

namespace App\Listeners;

use App\Events\ClientIssuesEvent;
use App\Dommain\BelongsToResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  ClientIssuesEvent  $event
     * @return void
     */
    public function handle(ClientIssuesEvent $event)
    {
        // here should send a email or a notification abut issue
        Log::error($event->getResponse()->getBody()->getContents());
    }
}
