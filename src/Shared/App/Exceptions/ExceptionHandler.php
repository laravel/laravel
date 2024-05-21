<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Exceptions;

use Closure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException as BaseRelationNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lightit\Shared\App\Exceptions\Http\HttpException;
use Lightit\Shared\App\Exceptions\Http\InvalidActionException;
use Lightit\Shared\App\Exceptions\Http\ModelNotFoundHttpException;
use Lightit\Shared\App\Exceptions\Http\PageNotFoundException;
use Lightit\Shared\App\Exceptions\Http\RelationNotFoundException;
use Lightit\Shared\App\Exceptions\Http\UnauthenticatedException;
use Lightit\Shared\App\Exceptions\Http\UnauthorizedException;
use Lightit\Shared\App\Exceptions\Http\ValidationFailedException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
            NotFoundHttpException::class => function (NotFoundHttpException $exception): void {
                $message = preg_replace('/\[.*\\\\([^\]]+)\]/', '$1', $exception->getMessage());

                throw new PageNotFoundException($message);
            },
            ModelNotFoundException::class => function (ModelNotFoundException $exception): void {
                $message = preg_replace('/\[.*\\\\([^\]]+)\]/', '$1', $exception->getMessage());

                throw new ModelNotFoundHttpException($message);
            },
            MethodNotAllowedHttpException::class => function (MethodNotAllowedHttpException $exception): void {
                throw new PageNotFoundException($exception->getMessage());
            },
            BaseRelationNotFoundException::class => RelationNotFoundException::class,
            ValidationException::class => function (ValidationException $exception): void {
                throw new ValidationFailedException($exception->getMessage(), $exception->validator);
            },
        ], array_flip($this->noConvert)));
    }

    public function getClosure(): Closure
    {
        return function (Exceptions $exceptions): void {
            Integration::handles($exceptions);

            $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $th) {
                if ($request->is('api/*')) {
                    return true;
                }

                return $request->expectsJson();
            });

            $exceptions->render(function (Exception $exception, Request $request): void {
                if ($request->wantsJson() || Str::of($request->path())->startsWith('api')) {
                    $this->convertDefaultException($exception);
                }
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
        return response()->json([
            'error' => [
                'code' => $exception->errorCode(),
                'message' => $exception->message(),
                ...($exception->data() ?? []),
            ],
        ], $exception->statusCode(), $exception->getHeaders());
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
