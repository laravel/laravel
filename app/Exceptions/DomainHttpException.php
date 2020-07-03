<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class DomainHttpException extends HttpException
{
    /** @var int */
    protected $status = Response::HTTP_UNPROCESSABLE_ENTITY;

    /** @var string|null */
    protected $errorCode = 'DOMAIN_EXCEPTION';
}
