<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\UnauthenticatedException as UnauthenticatedExceptionBase;

class UnauthenticatedException extends UnauthenticatedExceptionBase
{
    /** @var string|null */
    protected $errorCode = 'HTTP_UNAUTHENTICATED';

    /**
     * Construct the exception class.
     *
     * @param string|null $message
     * @param array|null  $headers
     */
    public function __construct(string $message = null, array $headers = null)
    {
        parent::__construct($message ?? trans('errors.unauthenticated'), $headers);
    }
}
