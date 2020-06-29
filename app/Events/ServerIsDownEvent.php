<?php

namespace App\Events;

use App\Dommain\BelongsToJob;
use App\Dommain\BelongsToResponse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Client\Response;
use Illuminate\Queue\SerializesModels;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ServerIsDownEvent implements BelongsToResponse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var ResponseInterface */
    private $response;
    /** @var string */
    private $queue;

    /**
     * Create a new event instance.
     *
     * @param string $nameQueue
     * @return void
     */
    public function __construct(ResponseInterface $response, $nameQueue)
    {
        $this->response = $response;
        $this->queueName = $nameQueue;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getNameQueue()
    {
        $this->queue;
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
