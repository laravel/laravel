<?php

namespace Illuminate\Http;

class StreamedEvent
{
    /**
     * The name of the event.
     */
    public string $event;

    /**
     * The data of the stream.
     */
    public mixed $data;

    /**
     * Create a new streamed event instance.
     */
    public function __construct(string $event, mixed $data)
    {
        $this->event = $event;
        $this->data = $data;
    }
}
