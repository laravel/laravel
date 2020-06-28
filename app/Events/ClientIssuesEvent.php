<?php

namespace App\Events;

use App\Dommain\BelongsToResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClientIssuesEvent implements BelongsToResponse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var ResponseInterface */
    private $response;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
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
