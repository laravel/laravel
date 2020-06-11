<?php

namespace App\Exceptions;

use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use ProjectName\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
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
     * {@inheritdoc}
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            if ($exception instanceof DomainException) {
                return $this->domainExceptionToResponse($exception);
            }

            if ($exception instanceof ValidationException) {
                return $this->validationExceptionToResponse($exception);
            }

            if ($exception instanceof EntityNotFoundException) {
                return $this->entityNotFoundExceptionToResponse();
            }

            if (! config('app.debug')) {
                return $this->internalServerErrorToResponse();
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * {@inheritdoc}
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return responder()
            ->error(Response::HTTP_UNAUTHORIZED, trans('errors.unauthenticated'))
            ->respond(Response::HTTP_UNAUTHORIZED);
    }

    private function domainExceptionToResponse(DomainException $exception): JsonResponse
    {
        $exception = new DomainExceptionDecorator($exception);

        return responder()
            ->error('DOMAIN_EXCEPTION', $exception->getMessage())
            ->respond(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function validationExceptionToResponse(ValidationException $exception): JsonResponse
    {
        $errors = $exception->validator->errors()->getMessages();

        $errors = [
            'validationErrors' => $errors,
        ];

        return responder()
            ->error('VALIDATION_EXCEPTION', trans('validation.fields_with_errors'))
            ->data($errors)
            ->respond(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function entityNotFoundExceptionToResponse(): JsonResponse
    {
        return responder()
            ->error('ENTITY_NOT_FOUND', trans('errors.entity_not_found'))
            ->respond(Response::HTTP_BAD_REQUEST);
    }

    private function internalServerErrorToResponse(): JsonResponse
    {
        return responder()
            ->error('UNEXPECTED_ERROR', trans('errors.unexpected_error'))
            ->respond(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function sentryReport(Throwable $exception): void
    {
        if ($this->shouldReport($exception) && config('logging.sentry_enabled') && app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}
