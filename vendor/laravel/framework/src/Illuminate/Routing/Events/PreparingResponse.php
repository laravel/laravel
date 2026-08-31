<?php

namespace Illuminate\Routing\Events;

class PreparingResponse
{
    /**
     * Create a new event instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request  The request instance.
     * @param  mixed  $response  The response instance.
     */
    public function __construct(
        public $request,
        public $response,
    ) {
    }
}
