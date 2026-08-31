<?php

namespace Illuminate\Http\Client\Events;

use Illuminate\Http\Client\Request;

class RequestSending
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Client\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param  \Illuminate\Http\Client\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
