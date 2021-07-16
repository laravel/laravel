<?php

namespace App\Http\Middleware;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordFormHandler;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Reminders\Reminder;
use Digbang\Security\Users\User;
use Illuminate\Http\Request;

class BackofficeRedirectExpiredPassword
{
    private SecurityApi $securityApi;

    public function __construct(SecurityApi $securityApi)
    {
        $this->securityApi = $securityApi;
    }

    public function handle(Request $request, \Closure $next)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasExpiredPassword()) {
            return redirect()
                ->to($this->buildPasswordChangeUrl($this->securityApi, $user))
                ->with('danger', trans('backoffice::auth.reset-password.expired'));
        }

        return $next($request);
    }

    private function buildPasswordChangeUrl(SecurityApi $securityApi, User $user)
    {
        /** @var Reminder $reminder */
        $reminder = $securityApi->reminders()->create($user);

        return AuthResetPasswordFormHandler::route($user->getUserId(), $reminder->getCode());
    }
}
