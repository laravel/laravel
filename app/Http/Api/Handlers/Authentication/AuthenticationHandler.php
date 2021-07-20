<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Exceptions\UnauthenticatedException;
use App\Http\Api\Handlers\Handler;
use App\Http\Api\Requests\Authentication\AuthenticationRequest;
use App\Http\Api\Transformers\TokenWithUserTransformer;
use App\Http\Kernel;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use ProjectName\Entities\User;

class AuthenticationHandler extends Handler
{
    public function __invoke(AuthenticationRequest $request): JsonResponse
    {
        $request->validate();

        $token = $this->guard()->attempt($request->credentials());
        if (! $token) {
            throw new UnauthenticatedException(trans('auth.failed'));
        }

        /** @var User $user */
        $user = $this->guard()->user();

        $response = (new TokenWithUserTransformer())->transform($token, $this->tokenTTL(), $user);

        return responder()
            ->success($response)
            ->respond();
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/authentication/authenticate', self::class)
            ->name(self::class)
            ->middleware(Kernel::PUBLIC_API);
    }
}
