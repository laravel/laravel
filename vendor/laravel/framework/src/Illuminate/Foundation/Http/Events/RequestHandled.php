<?php

namespace Illuminate\Foundation\Http\Events;

class RequestHandled
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * The response instance.
     *
     * @var \Illuminate\Http\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
