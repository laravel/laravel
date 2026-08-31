<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Auth\PasswordBroker;

/**
 * @method static \Illuminate\Contracts\Auth\PasswordBroker broker(string|null $name = null)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static string sendResetLink(array $credentials, \Closure|null $callback = null)
 * @method static mixed reset(array $credentials, \Closure $callback)
 * @method static \Illuminate\Contracts\Auth\CanResetPassword|null getUser(array $credentials)
 * @method static string createToken(\Illuminate\Contracts\Auth\CanResetPassword $user)
 * @method static void deleteToken(\Illuminate\Contracts\Auth\CanResetPassword $user)
 * @method static bool tokenExists(\Illuminate\Contracts\Auth\CanResetPassword $user, string $token)
 * @method static \Illuminate\Auth\Passwords\TokenRepositoryInterface getRepository()
 * @method static \Illuminate\Support\Timebox getTimebox()
 *
 * @see \Illuminate\Auth\Passwords\PasswordBrokerManager
 * @see \Illuminate\Auth\Passwords\PasswordBroker
 */
class Password extends Facade
{
    /**
     * Constant representing a successfully sent password reset email.
     *
     * @var string
     */
    const ResetLinkSent = PasswordBroker::RESET_LINK_SENT;

    /**
     * Constant representing a successfully reset password.
     *
     * @var string
     */
    const PasswordReset = PasswordBroker::PASSWORD_RESET;

    /**
     * Constant indicating the user could not be found when attempting a password reset.
     *
     * @var string
     */
    const InvalidUser = PasswordBroker::INVALID_USER;

    /**
     * Constant representing an invalid password reset token.
     *
     * @var string
     */
    const InvalidToken = PasswordBroker::INVALID_TOKEN;

    /**
     * Constant representing a throttled password reset attempt.
     *
     * @var string
     */
    const ResetThrottled = PasswordBroker::RESET_THROTTLED;

    const RESET_LINK_SENT = PasswordBroker::RESET_LINK_SENT;
    const PASSWORD_RESET = PasswordBroker::PASSWORD_RESET;
    const INVALID_USER = PasswordBroker::INVALID_USER;
    const INVALID_TOKEN = PasswordBroker::INVALID_TOKEN;
    const RESET_THROTTLED = PasswordBroker::RESET_THROTTLED;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.password';
    }
}
