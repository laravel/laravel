<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Http\Response;

class InternalServerErrorHttpException extends HttpException
{
    /** @var int */
    protected $status = Response::HTTP_INTERNAL_SERVER_ERROR;

    /** @var string|null */
    protected $errorCode = 'UNEXPECTED_ERROR';

    public function __construct()
    {
        parent::__construct(trans('errors.unexpected_error'));
    }
}
