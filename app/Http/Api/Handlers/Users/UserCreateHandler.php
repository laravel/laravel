<?php

namespace App\Http\Api\Handlers\Users;

use App\Http\Api\Handlers\Handler;
use App\Http\Api\Requests\Users\UserCreateRequest;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use ProjectName\Services\Users;

class UserCreateHandler extends Handler
{
    public function __invoke(UserCreateRequest $request, Users $users): JsonResponse
    {
        $request->validate();

        $users->create($request);

        return responder()
            ->success()
            ->respond(Response::HTTP_CREATED);
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/users', self::class)
            ->name(self::class)
            ->middleware(Kernel::PUBLIC_API);
    }
}
