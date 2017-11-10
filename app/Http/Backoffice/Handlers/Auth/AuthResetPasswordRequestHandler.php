<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordRequestRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthResetPasswordRequestHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';
    protected const ROUTE_NAME = 'backoffice.auth.password.reset-request';

    public function __invoke(
        ResetPasswordRequestRequest $request,
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

            return $redirector->to(DashboardIndexHandler::route())->with(
                'success', trans('backoffice::auth.reset-password.success', ['email' => $user->getEmail()])
            );
        }

        return $redirector->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    /** @param Router $router */
    public static function defineRoute(Router $router): void
    {
        $router
            ->post(config('backoffice.global_url_prefix') . '/auth/password/reset/{' . static::ROUTE_PARAM_USER . '}/{' . static::ROUTE_PARAM_CODE . '}', static::class)
            ->name(static::ROUTE_NAME)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE_PUBLIC,
            ]);
    }

    public static function route(int $userId, string $code): string
    {
        return route(static::ROUTE_NAME, [
            static::ROUTE_PARAM_USER => $userId,
            static::ROUTE_PARAM_CODE => $code,
        ]);
    }
}
