<?php

namespace App\Providers;

use App\Events\ClientIssuesEvent;
use App\Events\ServerIsDownEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Events\PackageCannotBeDeliveriedEvent;
use App\Listeners\LogAnErrorListener;
use App\Listeners\MoveJobsNextDayListener;
use App\Listeners\SendEmailServicesIssuesListener;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ServerIsDownEvent::class => [
            SendEmailServicesIssuesListener::class,
            MoveJobsNextDayListener::class,
        ],
        ClientIssuesEvent::class => [
            SendEmailServicesIssuesListener::class,
        ],
        PackageCannotBeDeliveriedEvent::class => [
            LogAnErrorListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
