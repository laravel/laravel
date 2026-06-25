<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->group('api', [
            ForceJsonResponse::class,
            ThrottleRequests::class.':api',
            SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(fn (ValidationException $exception): JsonResponse => ApiResponse::error(
            message: 'The given data was invalid.',
            status: Response::HTTP_UNPROCESSABLE_ENTITY,
            errors: $exception->errors(),
            code: 'VALIDATION_ERROR',
        ));

        $exceptions->render(fn (AuthenticationException $exception): JsonResponse => ApiResponse::error(
            message: 'Unauthenticated.',
            status: Response::HTTP_UNAUTHORIZED,
            code: 'UNAUTHENTICATED',
        ));

        $exceptions->render(fn (AuthorizationException $exception): JsonResponse => ApiResponse::error(
            message: 'This action is unauthorized.',
            status: Response::HTTP_FORBIDDEN,
            code: 'FORBIDDEN',
        ));

        $exceptions->render(fn (ModelNotFoundException|NotFoundHttpException $exception): JsonResponse => ApiResponse::error(
            message: 'The requested resource was not found.',
            status: Response::HTTP_NOT_FOUND,
            code: 'NOT_FOUND',
        ));

    $exceptions->render(fn(MethodNotAllowedHttpException $exception): JsonResponse => ApiResponse::error(
        message: 'The requested method is not allowed for this route.',
        status: Response::HTTP_METHOD_NOT_ALLOWED,
        code: 'METHOD_NOT_ALLOWED',
        errors: [
            'method' => [strtoupper(request()->getMethod())],
        ],
    ));

    $exceptions->render(fn (TooManyRequestsHttpException $exception): JsonResponse => ApiResponse::error(
            message: 'Too many requests.',
            status: Response::HTTP_TOO_MANY_REQUESTS,
            code: 'TOO_MANY_REQUESTS',
        ));
    })->create();
