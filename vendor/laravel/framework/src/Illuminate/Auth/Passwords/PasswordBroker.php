<?php

namespace Illuminate\Auth\Passwords;

use Closure;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Timebox;
use UnexpectedValueException;

class PasswordBroker implements PasswordBrokerContract
{
    /**
     * The password token repository.
     *
     * @var \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The timebox instance.
     *
     * @var \Illuminate\Support\Timebox
     */
    protected $timebox;

    /**
     * The number of microseconds that the timebox should wait for.
     *
     * @var int
     */
    protected $timeboxDuration;

    /**
     * Create a new password broker instance.
     *
     * @param  \Illuminate\Auth\Passwords\TokenRepositoryInterface  $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     * @param  \Illuminate\Support\Timebox|null  $timebox
     * @param  int  $timeboxDuration
     */
    public function __construct(
        #[\SensitiveParameter] TokenRepositoryInterface $tokens,
        UserProvider $users,
        ?Dispatcher $dispatcher = null,
        ?Timebox $timebox = null,
        int $timeboxDuration = 200000,
    ) {
        $this->users = $users;
        $this->tokens = $tokens;
        $this->events = $dispatcher;
        $this->timebox = $timebox ?: new Timebox;
        $this->timeboxDuration = $timeboxDuration;
    }

    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @param  \Closure|null  $callback
     * @return string
     */
    public function sendResetLink(#[\SensitiveParameter] array $credentials, ?Closure $callback = null)
    {
        return $this->timebox->call(function () use ($credentials, $callback) {
            // First we will check to see if we found a user at the given credentials and
            // if we did not we will redirect back to this current URI with a piece of
            // "flash" data in the session to indicate to the developers the errors.
            $user = $this->getUser($credentials);

            if (is_null($user)) {
                return static::INVALID_USER;
            }

            if ($this->tokens->recentlyCreatedToken($user)) {
                return static::RESET_THROTTLED;
            }

            $token = $this->tokens->create($user);

            if ($callback) {
                return $callback($user, $token) ?? static::RESET_LINK_SENT;
            }

            // Once we have the reset token, we are ready to send the message out to this
            // user with a link to reset their password. We will then redirect back to
            // the current URI having nothing set in the session to indicate errors.
            $user->sendPasswordResetNotification($token);

            $this->events?->dispatch(new PasswordResetLinkSent($user));

            return static::RESET_LINK_SENT;
        }, $this->timeboxDuration);
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return string
     */
    public function reset(#[\SensitiveParameter] array $credentials, Closure $callback)
    {
        return $this->timebox->call(function ($timebox) use ($credentials, $callback) {
            $user = $this->validateReset($credentials);

            // If the responses from the validate method is not a user instance, we will
            // assume that it is a redirect and simply return it from this method and
            // the user is properly redirected having an error message on the post.
            if (! $user instanceof CanResetPasswordContract) {
                return $user;
            }

            $password = $credentials['password'];

            // Once the reset has been validated, we'll call the given callback with the
            // new password. This gives the user an opportunity to store the password
            // in their persistent storage. Then we'll delete the token and return.
            $callback($user, $password);

            $this->tokens->delete($user);

            $timebox->returnEarly();

            return static::PASSWORD_RESET;
        }, $this->timeboxDuration);
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(#[\SensitiveParameter] array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     *
     * @throws \UnexpectedValueException
     */
    public function getUser(#[\SensitiveParameter] array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function createToken(CanResetPasswordContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete password reset tokens of the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function deleteToken(CanResetPasswordContract $user)
    {
        $this->tokens->delete($user);
    }

    /**
     * Validate the given password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function tokenExists(CanResetPasswordContract $user, #[\SensitiveParameter] $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the password reset token repository implementation.
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    public function getRepository()
    {
        return $this->tokens;
    }

    /**
     * Get the timebox instance used by the guard.
     *
     * @return \Illuminate\Support\Timebox
     */
    public function getTimebox()
    {
        return $this->timebox;
    }
}
