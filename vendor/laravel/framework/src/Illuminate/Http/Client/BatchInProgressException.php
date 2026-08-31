<?php

namespace Illuminate\Http\Client;

class BatchInProgressException extends HttpClientException
{
    public function __construct()
    {
        parent::__construct('You cannot add requests to a batch that is already in progress.');
    }
}
