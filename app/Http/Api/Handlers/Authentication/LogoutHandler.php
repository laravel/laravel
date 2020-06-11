<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Http\Api\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Tymon\JWTAuth\JWTGuard;

class LogoutHandler extends Handler implements RouteDefiner
{
    public function __invoke(AuthManager $auth): JsonResponse
    {
        /** @var JWTGuard $guard */
        $guard = $auth->guard(self::GUARD);

        $guard->logout();

        return responder()
            ->success()
            ->respond(Response::HTTP_NO_CONTENT);
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/authentication/logout', self::class)
            ->name(self::class)
            ->middleware(Kernel::API);
    }
}
