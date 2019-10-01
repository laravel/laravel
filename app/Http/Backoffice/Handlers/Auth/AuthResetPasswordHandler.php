<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthResetPasswordHandler extends Handler implements RouteDefiner
{
    protected const ROUTE_NAME = 'backoffice.auth.password.reset';

    public function __invoke(
        int $userId,
        string $resetCode,
        SecurityApi $securityApi,
        Redirector $redirector,
        Factory $view
    ) {
        /** @var User $user */
        $user = $securityApi->users()->findById($userId) ?: abort(404);

        if ($securityApi->reminders()->exists($user, $resetCode)) {
            return $view->make('backoffice::auth.reset-password', [
                'id' => $user->getUserId(),
                'resetCode' => $resetCode,
            ]);
        }

        return $redirector->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    public static function defineRoute(Router $router)
    {
        $router
            ->get(config('backoffice.global_url_prefix') . '/auth/password/reset/{user_id}/{code}', static::class)
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
