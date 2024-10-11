<?php

namespace App\Exception;

use App\Traits\ApiResponses;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class ConnectionInterruptionException extends ConnectionException
{
    use ApiResponses;


    // Message, int code, nullable throwable
    public function __construct(
        string $message = 'We ran into a problem communicating with the service.  Our team is looking into it - Please check back soon!',
        int $code = 504,
        ?Throwable $previous = null
    )
    {
        parent::__construct(
            $message,
            $code,
            $previous,
        );
    }

}
