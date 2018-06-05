<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthResetPasswordHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';
    protected const ROUTE_NAME = 'backoffice.auth.password.reset-request';

    public function __invoke(
        ResetPasswordRequest $request,
        SecurityApi $securityApi,
        Redirector $redirector
    ) {
        $user = $request->getUser();
        $code = $request->getCode();

        if ($user->getUserId() != $request->input('id')) {
            return $redirector->to(AuthLoginHandler::route());
        }

        $reminders = $securityApi->reminders();

        if ($reminders->exists($user, $code)) {
            $reminders->complete($user, $code, $request->input('password'));

            $securityApi->login($user);

            return $redirector->to(DashboardHandler::route())->with(
                'success', trans('backoffice::auth.reset-password.success', ['email' => $user->getEmail()])
            );
        }

        return $redirector->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    /** @param Router $router */
    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post($backofficePrefix . '/auth/password/reset/{' . static::ROUTE_PARAM_USER . '}/{' . static::ROUTE_PARAM_CODE . '}', static::class)
            ->name(static::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(int $userId, string $code): string
    {
        return route(static::ROUTE_NAME, [
            static::ROUTE_PARAM_USER => $userId,
            static::ROUTE_PARAM_CODE => $code,
        ]);
    }
}
