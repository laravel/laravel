<?php

namespace App\Events;

use App\Dommain\BelongsToError;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageCannotBeDeliveriedEvent implements BelongsToError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var string */
    private $error;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
