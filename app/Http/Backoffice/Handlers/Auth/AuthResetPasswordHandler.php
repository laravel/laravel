<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class AuthResetPasswordHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';
    protected const ROUTE_NAME = 'backoffice.auth.password.reset-request';

    public function __invoke(
        ResetPasswordRequest $request,
        SecurityApi $securityApi
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->findUser();
        $code = $request->code();
        $password = $request->password();

        $reminders = $securityApi->reminders();
        if ($reminders->exists($user, $code)) {
            $reminders->complete($user, $code, $password);

            $securityApi->login($user);

            return redirect()->to(DashboardHandler::route())->with(
                'success', trans('backoffice::auth.reset-password.success', ['email' => $user->getEmail()])
            );
        }

        return redirect()->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post($backofficePrefix . '/auth/password/reset/{' . self::ROUTE_PARAM_USER . '}/{' . self::ROUTE_PARAM_CODE . '}', self::class)
            ->name(self::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(int $userId, string $code): string
    {
        return route(self::ROUTE_NAME, [
            self::ROUTE_PARAM_USER => $userId,
            self::ROUTE_PARAM_CODE => $code,
        ]);
    }
}
