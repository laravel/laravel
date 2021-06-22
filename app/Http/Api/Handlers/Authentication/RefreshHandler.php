<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Http\Api\Handlers\Handler;
use App\Http\Api\Transformers\TokenTransformer;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;

class RefreshHandler extends Handler
{
    public function __invoke(): JsonResponse
    {
        $token = $this->guard()->refresh();
        if ($token) {
            $response = (new TokenTransformer())->transform($token, $this->tokenTTL());

            return responder()
                ->success($response)
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
