<?php

namespace Illuminate\Routing\Events;

class ResponsePrepared
{
    /**
     * Create a new event instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request  The request instance.
     * @param  \Symfony\Component\HttpFoundation\Response  $response  The response instance.
     */
    public function __construct(
        public $request,
        public $response,
    ) {
    }
}
