<?php

namespace App\Http\Api\Handlers\Users;

use App\Http\Api\Handlers\Handler;
use App\Http\Api\Transformers\UserProfileTransformer;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;

class UserProfileHandler extends Handler
{
    public function __invoke(): JsonResponse
    {
        $user = $this->user();

        return responder()
            ->success($user, UserProfileTransformer::class)
            ->meta([
                'meta' => $this->shouldRefreshToken(),
            ])
            ->respond();
    }

    public static function defineRoute(Router $router): void
    {
        $router->get('api/users/profile', self::class)
            ->name(self::class)
            ->middleware(Kernel::API);
    }
}
