<?php

namespace Illuminate\Auth\Passwords;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CacheTokenRepository implements TokenRepositoryInterface
{
    /**
     * The format of the stored Carbon object.
     */
    protected string $format = 'Y-m-d H:i:s';

    /**
     * Create a new token repository instance.
     */
    public function __construct(
        protected Repository $cache,
        protected HasherContract $hasher,
        protected string $hashKey,
        protected int $expires = 3600,
        protected int $throttle = 60,
    ) {
    }

    /**
     * Create a new token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $this->delete($user);

        $token = hash_hmac('sha256', Str::random(40), $this->hashKey);

        $this->cache->put(
            $this->cacheKey($user),
            [$this->hasher->make($token), Carbon::now()->format($this->format)],
            $this->expires,
        );

        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, #[\SensitiveParameter] $token)
    {
        [$record, $createdAt] = $this->cache->get($this->cacheKey($user));

        return $record
            && ! $this->tokenExpired($createdAt)
            && $this->hasher->check($token, $record);
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::createFromFormat($this->format, $createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        [$record, $createdAt] = $this->cache->get($this->cacheKey($user));

        return $record && $this->tokenRecentlyCreated($createdAt);
    }

    /**
     * Determine if the token was recently created.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenRecentlyCreated($createdAt)
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::createFromFormat($this->format, $createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }

    /**
     * Delete a token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->cache->forget($this->cacheKey($user));
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
    }

    /**
     * Determine the cache key for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function cacheKey(CanResetPasswordContract $user): string
    {
        return hash('sha256', $user->getEmailForPasswordReset());
    }
}
