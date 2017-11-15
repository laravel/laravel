<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordFormRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthResetPasswordFormHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';
    protected const ROUTE_NAME = 'backoffice.auth.password.reset';

    public function __invoke(
        ResetPasswordFormRequest $request,
        SecurityApi $securityApi,
        Redirector $redirector,
        Factory $view
    ) {
        $user = $request->getUser();
        $code = $request->getCode();

        if ($securityApi->reminders()->exists($user, $code)) {
            return $view->make('backoffice::auth.reset-password', [
                'id' => $user->getUserId(),
                'resetCode' => $code,
            ]);
        }

        return $redirector->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    public static function defineRoute(Router $router): void
    {
        $router
            ->get(config('backoffice.global_url_prefix') . '/auth/password/reset/{' . static::ROUTE_PARAM_USER . '}/{' . static::ROUTE_PARAM_CODE . '}', static::class)
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
