<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions;

use Closure;
use Exception;
use Flugg\Responder\Contracts\Responder;
use Flugg\Responder\Exceptions\Http\HttpException;
use Flugg\Responder\Exceptions\Http\PageNotFoundException;
use Flugg\Responder\Exceptions\Http\RelationNotFoundException;
use Flugg\Responder\Exceptions\Http\UnauthenticatedException;
use Flugg\Responder\Exceptions\Http\UnauthorizedException;
use Flugg\Responder\Exceptions\Http\ValidationFailedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException as BaseRelationNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionHandler
{
    /** @var array<int, string> */
    private array $noConvert = [];

    protected function convertDefaultException(Exception $exception): void
    {
        $this->convert($exception, array_diff_key([
            AuthenticationException::class => UnauthenticatedException::class,
            AuthorizationException::class => UnauthorizedException::class,
            AccessDeniedHttpException::class => UnauthorizedException::class,
            NotFoundHttpException::class => PageNotFoundException::class,
            ModelNotFoundException::class => ModelNotFoundHttpException::class,
            BaseRelationNotFoundException::class => RelationNotFoundException::class,
            ValidationException::class => function (ValidationException $exception) {
                throw new ValidationFailedException($exception->validator);
            },
        ], array_flip($this->noConvert)));
    }

    public function getClosure(): Closure
    {
        return function (Exceptions $exceptions) {
            Integration::handles($exceptions);

            $exceptions->render(function (Exception $exception) {
                $this->convertDefaultException($exception);
            });

            $exceptions->render(function (HttpException $exception, Request $request) {
                if ($request->wantsJson() || Str::of($request->path())->startsWith('api')) {
                    return $this->renderResponse($exception);
                }
            });
        };
    }

    protected function renderResponse(HttpException $exception): JsonResponse
    {
        // @phpstan-ignore-next-line
        return app(Responder::class)
            ->error($exception->errorCode(), $exception->message())
            ->data($exception->data())
            ->respond($exception->statusCode(), $exception->getHeaders());
    }

    /**
     * @param array<class-string<Exception>, callable|class-string<Exception>> $convert
     *
     * @throws Exception
     */
    protected function convert(Exception $exception, array $convert): void
    {
        foreach ($convert as $source => $target) {
            if ($exception instanceof $source) {
                if (is_callable($target)) {
                    $target($exception);
                }

                if (is_string($target) && is_subclass_of($target, Exception::class)) {
                    throw new $target();
                }

                throw new InvalidActionException('Invalid target provided for exception conversion.');
            }
        }
    }
}
