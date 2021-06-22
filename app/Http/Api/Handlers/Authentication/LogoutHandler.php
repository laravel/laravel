<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Http\Api\Handlers\Handler;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;

class LogoutHandler extends Handler
{
    public function __invoke(): JsonResponse
    {
        $this->guard()->logout();

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
