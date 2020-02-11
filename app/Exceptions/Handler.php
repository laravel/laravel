<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use ProjectName\Exceptions\DomainException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    public function report(Exception $exception)
    {
        if (config('logging.sentry_enabled') && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof DomainException) {
            $exception = new DomainExceptionDecorator($exception);
        }

        return parent::render($request, $exception);
    }
}
