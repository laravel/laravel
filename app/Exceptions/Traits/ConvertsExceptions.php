<?php

namespace App\Exceptions\Traits;

use App\Exceptions\DomainHttpException;
use App\Exceptions\EntityNotFoundHttpException;
use App\Exceptions\PageNotFoundException;
use App\Exceptions\UnauthenticatedException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationFailedException;
use Doctrine\ORM\EntityNotFoundException;
use Flugg\Responder\Contracts\Responder;
use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use ProjectName\Exceptions\DomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait ConvertsExceptions
{
    /**
     * Convert a default exception to an API exception.
     *
     * @return void
     */
    protected function convertDefaultException(Throwable $exception)
    {
        $this->convert($exception, array_diff_key([
            AuthenticationException::class => UnauthenticatedException::class,
            AuthorizationException::class => UnauthorizedException::class,
            NotFoundHttpException::class => PageNotFoundException::class,
            ModelNotFoundException::class => PageNotFoundException::class,
            DomainException::class => DomainHttpException::class,
            EntityNotFoundException::class => EntityNotFoundHttpException::class,
            ValidationException::class => function ($exception) {
                throw new ValidationFailedException($exception->validator);
            },
        ], array_flip($this->dontConvert)));
    }

    /**
     * Convert an exception to another exception.
     *
     * @return void
     */
    protected function convert(Throwable $exception, array $convert)
    {
        foreach ($convert as $source => $target) {
            if ($exception instanceof $source) {
                if (is_callable($target)) {
                    $target($exception);
                }

                throw new $target;
            }
        }
    }

    /**
     * Render an error response from an API exception.
     */
    protected function renderResponse(HttpException $exception): JsonResponse
    {
        return app(Responder::class)
            ->error($exception->errorCode(), $exception->message())
            ->data($exception->data())
            ->respond($exception->statusCode(), $exception->getHeaders());
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
