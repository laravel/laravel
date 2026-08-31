<?php

namespace Laravel\Sanctum;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Events\TokenAuthenticated;

class Guard
{
    /**
     * Create a new guard instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth  The authentication factory implementation.
     * @param  int  $expiration  The number of minutes tokens should be allowed to remain valid.
     * @param  string  $provider  The provider name.
     * @param  bool  $trackLastUsedAt  Whether to track the last used timestamp.
     */
    public function __construct(protected AuthFactory $auth, protected $expiration = null, protected $provider = null, protected $trackLastUsedAt = true)
    {
    }

    /**
     * Retrieve the authenticated user for the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        foreach (Arr::wrap(config('sanctum.guard', 'web')) as $guard) {
            if ($user = $this->auth->guard($guard)->user()) {
                return $this->supportsTokens($user)
                    ? $user->withAccessToken(new TransientToken)
                    : $user;
            }
        }

        if ($token = $this->getTokenFromRequest($request)) {
            $model = Sanctum::$personalAccessTokenModel;

            $accessToken = $model::findToken($token);

            if (! $this->isValidAccessToken($accessToken) ||
                ! $this->supportsTokens($accessToken->tokenable)) {
                return;
            }

            $tokenable = $accessToken->tokenable->withAccessToken(
                $accessToken
            );

            event(new TokenAuthenticated($accessToken));

            if ($this->trackLastUsedAt) {
                $this->updateLastUsedAt($accessToken);
            }

            return $tokenable;
        }
    }

    /**
     * Determine if the tokenable model supports API tokens.
     *
     * @param  mixed  $tokenable
     * @return bool
     */
    protected function supportsTokens($tokenable = null)
    {
        return $tokenable && in_array(HasApiTokens::class, class_uses_recursive(
            get_class($tokenable)
        ));
    }

    /**
     * Get the token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getTokenFromRequest(Request $request)
    {
        if (is_callable(Sanctum::$accessTokenRetrievalCallback)) {
            return (string) (Sanctum::$accessTokenRetrievalCallback)($request);
        }

        $token = $request->bearerToken();

        return $this->isValidBearerToken($token) ? $token : null;
    }

    /**
     * Determine if the bearer token is in the correct format.
     *
     * @param  string|null  $token
     * @return bool
     */
    protected function isValidBearerToken(?string $token = null)
    {
        if (! is_null($token) && str_contains($token, '|')) {
            $model = new Sanctum::$personalAccessTokenModel;

            if ($model->getKeyType() === 'int') {
                [$id, $token] = explode('|', $token, 2);

                return ctype_digit($id) && ! empty($token);
            }
        }

        return ! empty($token);
    }

    /**
     * Determine if the provided access token is valid.
     *
     * @param  mixed  $accessToken
     * @return bool
     */
    protected function isValidAccessToken($accessToken): bool
    {
        if (! $accessToken) {
            return false;
        }

        $isValid =
            (! $this->expiration || $accessToken->created_at->gt(now()->subMinutes($this->expiration)))
            && (! $accessToken->expires_at || ! $accessToken->expires_at->isPast())
            && $this->hasValidProvider($accessToken->tokenable);

        if (is_callable(Sanctum::$accessTokenAuthenticationCallback)) {
            $isValid = (bool) (Sanctum::$accessTokenAuthenticationCallback)($accessToken, $isValid);
        }

        return $isValid;
    }

    /**
     * Determine if the tokenable model matches the provider's model type.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $tokenable
     * @return bool
     */
    protected function hasValidProvider($tokenable)
    {
        if (is_null($this->provider)) {
            return true;
        }

        $model = config("auth.providers.{$this->provider}.model");

        return $tokenable instanceof $model;
    }

    /**
     * Store the time the token was last used.
     *
     * @param  \Laravel\Sanctum\PersonalAccessToken  $accessToken
     * @return void
     */
    protected function updateLastUsedAt($accessToken)
    {
        if (method_exists($accessToken->getConnection(), 'hasModifiedRecords') &&
            method_exists($accessToken->getConnection(), 'setRecordModificationState')) {
            $hasModifiedRecords = $accessToken->getConnection()->hasModifiedRecords();
            $accessToken->forceFill(['last_used_at' => now()])->save();

            $accessToken->getConnection()->setRecordModificationState($hasModifiedRecords);
        } else {
            $accessToken->forceFill(['last_used_at' => now()])->save();
        }
    }
}
