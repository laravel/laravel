<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Broadcast::routes();

        /*
         |--------------------------------------------------------------------------
         | Broadcast Channels
         |--------------------------------------------------------------------------
         |
         | Here you may register all of the event broadcasting channels that your
         | application supports. The given channel authorization callbacks are
         | used to check if an authenticated user can listen to the channel.
         |
         */
        Broadcast::channel('App.User.{id}', function ($user, $id) {
            return (int) $user->id === (int) $id;
        });
    }
}
