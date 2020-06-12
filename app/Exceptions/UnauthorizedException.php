<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\UnauthorizedException as UnauthorizedExceptionBase;

class UnauthorizedException extends UnauthorizedExceptionBase
{
    public function __construct()
    {
        parent::__construct(trans('errors.unauthenticated'));
    }
}
