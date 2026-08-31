<?php

namespace Illuminate\Auth\Passwords;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

interface TokenRepositoryInterface
{
    /**
     * Create a new token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, #[\SensitiveParameter] $token);

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user);

    /**
     * Delete a token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function delete(CanResetPasswordContract $user);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();
}
