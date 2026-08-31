<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection as BaseCollection;

class BroadcastableModelEventOccurred implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * The model instance corresponding to the event.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * The event name (created, updated, etc.).
     *
     * @var string
     */
    protected $event;

    /**
     * The channels that the event should be broadcast on.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The queue connection that should be used to queue the broadcast job.
     *
     * @var string
     */
    public $connection;

    /**
     * The queue that should be used to queue the broadcast job.
     *
     * @var string
     */
    public $queue;

    /**
     * Indicates whether the job should be dispatched after all database transactions have committed.
     *
     * @var bool|null
     */
    public $afterCommit;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $event
     */
    public function __construct($model, $event)
    {
        $this->model = $model;
        $this->event = $event;
    }

    /**
     * The channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        $channels = empty($this->channels)
            ? ($this->model->broadcastOn($this->event) ?: [])
            : $this->channels;

        return (new BaseCollection($channels))
            ->map(fn ($channel) => $channel instanceof Model ? new PrivateChannel($channel) : $channel)
            ->all();
    }

    /**
     * The name the event should broadcast as.
     *
     * @return string
     */
    public function broadcastAs()
    {
        $default = class_basename($this->model).ucfirst($this->event);

        return method_exists($this->model, 'broadcastAs')
            ? ($this->model->broadcastAs($this->event) ?: $default)
            : $default;
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     *
     * @return array|null
     */
    public function broadcastWith()
    {
        return method_exists($this->model, 'broadcastWith')
            ? $this->model->broadcastWith($this->event)
            : null;
    }

    /**
     * Manually specify the channels the event should broadcast on.
     *
     * @param  array  $channels
     * @return $this
     */
    public function onChannels(array $channels)
    {
        $this->channels = $channels;

        return $this;
    }

    /**
     * Determine if the event should be broadcast synchronously.
     *
     * @return bool
     */
    public function shouldBroadcastNow()
    {
        return $this->event === 'deleted' &&
               ! method_exists($this->model, 'bootSoftDeletes');
    }

    /**
     * Get the event name.
     *
     * @return string
     */
    public function event()
    {
        return $this->event;
    }
}
