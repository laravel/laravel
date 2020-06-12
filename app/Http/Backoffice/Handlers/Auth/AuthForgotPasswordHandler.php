<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Requests\Auth\ForgotPasswordRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class AuthForgotPasswordHandler extends Handler implements RouteDefiner
{
    use SendsEmails;

    protected const ROUTE_NAME = 'backoffice.auth.password.forgot-request';

    public function __invoke(
        ForgotPasswordRequest $request,
        SecurityApi $securityApi
    ): RedirectResponse {
        $email = $request->getEmail();

        /** @var User|null $user */
        $user = $securityApi->users()->findByCredentials(['email' => $email]);
        if (! $user) {
            return redirect()->back()
                ->withErrors(['email' => trans('backoffice::auth.validation.user.not-found')]);
        }

        /** @var \Digbang\Security\Reminders\Reminder $reminder */
        $reminder = $securityApi->reminders()->create($user);

        $this->sendPasswordReset(
            $user,
            AuthResetPasswordFormHandler::route($user->getUserId(), $reminder->getCode())
        );

        return redirect()->to(AuthLoginHandler::route())
            ->with('info', trans('backoffice::auth.reset-password.email-sent',
                ['email' => $user->getEmail()]
            ));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post("$backofficePrefix/auth/password/forgot", self::class)
            ->name(self::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(self::ROUTE_NAME);
    }
}
