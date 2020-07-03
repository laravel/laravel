<?php

namespace App\Exceptions;

use Doctrine\ORM\EntityNotFoundException;
use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Throwable;

class Handler extends ExceptionHandler
{
    use Traits\ConvertsExceptions;

    /**
     * A list of default exception types that should not be converted.
     */
    protected array $dontConvert = [
        ModelNotFoundException::class,
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        SuspiciousOperationException::class,
        TokenMismatchException::class,
        ValidationException::class,
        EntityNotFoundException::class,
    ];

    public function report(Throwable $exception)
    {
        $this->sentryReport($exception);

        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Throwable|\Exception $exception
     *
     * @throws Throwable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson()) {
            $this->convertDefaultException($exception);

            if ($exception instanceof HttpException) {
                return $this->renderResponse($exception);
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Report to Sentry Service.
     */
    private function sentryReport(Throwable $exception): void
    {
        if ($this->shouldReport($exception) && config('logging.sentry_enabled') && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
