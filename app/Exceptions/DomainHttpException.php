<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class DomainHttpException extends HttpException
{
    protected $status = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected $errorCode = 'DOMAIN_EXCEPTION';

    public function __construct()
    {
        parent::__construct(trans('exception.entity.notFound'));
    }
}
