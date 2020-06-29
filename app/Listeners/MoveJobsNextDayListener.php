<?php

namespace App\Listeners;

use App\Job;
use Carbon\Carbon;
use App\Events\Quesadilla;
use App\Events\ServerIsDownEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MoveJobsNextDayListener
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
     * @param  ServerIsDownEvent  $event
     * @return void
     */
    public function handle(ServerIsDownEvent $event)
    {
        /** @var string */
        $name = $event->getNameQueue();

        Job::where('queue', $name)->JobsOfToday()->update([
            'available_at' => Carbon::now('UTC')->addDay(),
        ]);
    }
}
