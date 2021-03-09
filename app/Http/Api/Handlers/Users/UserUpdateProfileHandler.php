<?php

namespace App\Http\Api\Handlers\Users;

use App\Http\Api\Handlers\Handler;
use App\Http\Api\Requests\Users\UserUpdateProfileRequest;
use App\Http\Api\Transformers\UserProfileTransformer;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use ProjectName\Services\Users;

class UserUpdateProfileHandler extends Handler
{
    public function __invoke(UserUpdateProfileRequest $request, Users $users): JsonResponse
    {
        $user = $this->user();

        $request->validate($user);

        $user = $users->update($user, $request);

        return responder()
            ->success($user, UserProfileTransformer::class)
            ->meta([
                'meta' => $this->shouldRefreshToken(),
            ])
            ->respond();
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/users/profile', self::class)
            ->name(self::class)
            ->middleware(Kernel::API);
    }
}
