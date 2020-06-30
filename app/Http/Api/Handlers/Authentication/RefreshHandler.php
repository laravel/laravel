<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Http\Api\Handlers\Handler;
use App\Http\Kernel;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

class RefreshHandler extends Handler
{
    public function __invoke(AuthManager $auth, JWTAuth $jwtAuth): JsonResponse
    {
        /** @var JWTGuard $guard */
        $guard = $auth->guard(self::GUARD);

        $token = $guard->refresh();
        if ($token) {
            return responder()
                ->success([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $jwtAuth->factory()->getTTL() * 60,
                ])
                ->respond();
        }

        return responder()
            ->error(Response::HTTP_UNAUTHORIZED, trans('errors.unauthenticated'))
            ->respond(Response::HTTP_UNAUTHORIZED);
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/authentication/refresh', self::class)
            ->name(self::class)
            ->middleware(Kernel::API);
    }

    public static function route(): string
    {
        return route(self::class);
    }
}
