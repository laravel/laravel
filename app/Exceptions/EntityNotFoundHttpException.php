<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class EntityNotFoundHttpException extends HttpException
{
    protected $status = Response::HTTP_BAD_REQUEST;

    protected $errorCode = 'ENTITY_NOT_FOUND';

    public function __construct()
    {
        parent::__construct(trans('exception.entity.notFound'));
    }
}
