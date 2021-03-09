<?php

namespace App\Http\Api\Handlers;

use App\Http\Api\Transformers\TokenTransformer;
use App\Http\Utils\RouteDefiner;
use Cake\Chronos\Chronos;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Controller as BaseHandler;
use Lcobucci\JWT\Parser;
use ProjectName\Entities\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

abstract class Handler extends BaseHandler implements RouteDefiner
{
    public const GUARD = 'api';

    private Container $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public static function routePriority(): int
    {
        return 0;
    }

    protected function shouldRefreshToken(): array
    {
        /** @var JWTAuth $jwtAuth */
        $jwtAuth = $this->app->make(JWTAuth::class);
        /** @var Parser $jwtParser */
        $jwtParser = $this->app->make(Parser::class);

        $token = $jwtAuth->getToken();
        if ($token) {
            $parsedToken = $jwtParser->parse($token->get());

            if ($parsedToken->isExpired((new Chronos())->subMinutes(config('jwt.auto_refresh_ttl') * 60))) {
                $newToken = $this->guard()->refresh();

                return [
                    'token' => (new TokenTransformer())->transform($newToken, $this->tokenTTL()),
                ];
            }
        }

        return [];
    }

    protected function user(): User
    {
        /** @var User $user */
        $user = $this->guard()->user();

        return $user;
    }

    protected function tokenTTL(): int
    {
        /** @var JWTAuth $jwtAuth */
        $jwtAuth = $this->app->make(JWTAuth::class);

        return $jwtAuth->factory()->getTTL() * 60;
    }

    protected function guard(): JWTGuard
    {
        /** @var AuthManager $authManager */
        $authManager = $this->app->make(AuthManager::class);

        /** @var JWTGuard $guard */
        $guard = $authManager->guard(self::GUARD);

        return $guard;
    }
}
