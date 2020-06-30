<?php

namespace App\Http\Api\Handlers;

use App\Http\Api\Transformers\TokenTransformer;
use App\Http\Utils\RouteDefiner;
use Cake\Chronos\Chronos;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseHandler;
use Lcobucci\JWT\Parser;
use ProjectName\Entities\User;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\JWTGuard;

abstract class Handler extends BaseHandler implements RouteDefiner
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public const GUARD = 'api';

    protected AuthManager $auth;
    private JWTAuth $jwtAuth;
    private Parser $jwtParser;

    public function __construct(AuthManager $auth, JWTAuth $jwtAuth, Parser $jwtParser)
    {
        $this->auth = $auth;
        $this->jwtAuth = $jwtAuth;
        $this->jwtParser = $jwtParser;
    }

    public static function routePriority(): int
    {
        return 0;
    }

    protected function shouldRefreshToken(): array
    {
        $token = $this->jwtAuth->getToken();
        if ($token) {
            $parsedToken = $this->jwtParser->parse($token->get());

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
        return $this->jwtAuth->factory()->getTTL() * 60;
    }

    protected function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = $this->auth->guard(self::GUARD);

        return $guard;
    }
}
