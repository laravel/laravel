<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthResetPasswordRequestHandler extends Handler implements RouteDefiner
{
    protected const ROUTE_NAME = 'backoffice.auth.password.reset-request';

    public function __invoke(
        int $userId,
        string $resetCode,
        ResetPasswordRequest $request,
        SecurityApi $securityApi,
        Redirector $redirector
    ) {
        /** @var User $user */
        $user = $securityApi->users()->findById($userId) ?: abort(404);

        if ($user->getUserId() != $request->input('id')) {
            return $redirector->to(AuthLoginHandler::route());
        }

        $reminders = $securityApi->reminders();

        if ($reminders->exists($user, $resetCode)) {
            $reminders->complete($user, $resetCode, $request->input('password'));

            $securityApi->login($user);

            return $redirector->to(DashboardIndexHandler::route())->with(
                'success', trans('backoffice::auth.reset-password.success', ['email' => $user->getEmail()])
            );
        }

        return $redirector->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    /** @param Router $router */
    public static function defineRoute(Router $router)
    {
        $router
            ->post(config('backoffice.global_url_prefix') . '/auth/password/reset/{user_id}/{code}', static::class)
            ->name(static::ROUTE_NAME)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE_PUBLIC,
            ]);
    }

    public static function route($userId, $code)
    {
        return route(static::ROUTE_NAME, [
            'user_id' => $userId,
            'code' => $code,
        ]);
    }
}
