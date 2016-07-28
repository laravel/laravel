<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\BroadcastServiceProvider as ServiceProvider;
use Illuminate\Contracts\Broadcasting\Factory as BroadcasterContract;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * The channel auth handler mappings for the application.
     *
     * @var array
     */
    protected $channels = [
        'channel-name.*' => [
            'App\Broadcasting\Authenticator@someChannelName',
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BroadcasterContract $broadcaster)
    {
        parent::boot($broadcaster);

        $broadcaster->route(['middleware' => ['web']]);

        $broadcaster->auth('channel-name.*', function ($user, $id) {
            return true;
        });
    }
}
