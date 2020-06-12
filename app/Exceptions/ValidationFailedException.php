<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\ValidationFailedException as ValidationFailedExceptionBase;
use Illuminate\Http\Response;

class ValidationFailedException extends ValidationFailedExceptionBase
{
    /**
     * An HTTP status code.
     *
     * @var int
     */
    protected $status = Response::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * An error code.
     *
     * @var string|null
     */
    protected $errorCode = 'VALIDATION_EXCEPTION';
}
