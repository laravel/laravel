<?php

namespace App\Http\Api\Handlers\Authentication;

use App\Http\Api\Handlers\Handler;
use App\Http\Api\Requests\Authentication\AuthenticationRequest;
use App\Http\Api\Transformers\TokenWithUserTransformer;
use App\Http\Utils\RouteDefiner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use ProjectName\Entities\User;

class AuthenticationHandler extends Handler implements RouteDefiner
{
    public function __invoke(AuthenticationRequest $request): JsonResponse
    {
        $token = $this->guard()->attempt($request->credentials());
        if ($token) {
            /** @var User $user */
            $user = $this->guard()->user();

            $response = (new TokenWithUserTransformer())->transform($token, $this->tokenTTL(), $user);

            return responder()
                ->success($response)
                ->respond();
        }

        return responder()
            ->error(Response::HTTP_UNAUTHORIZED, trans('exceptions.authentication.credentials'))
            ->respond(Response::HTTP_UNAUTHORIZED);
    }

    public static function defineRoute(Router $router): void
    {
        $router->post('api/authentication/authenticate', self::class)
            ->name(self::class);
    }
}
