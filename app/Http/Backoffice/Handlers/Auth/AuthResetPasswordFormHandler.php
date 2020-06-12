<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ResetPasswordFormRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Routing\Router;

class AuthResetPasswordFormHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';
    protected const ROUTE_NAME = 'backoffice.auth.password.reset';

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(
        ResetPasswordFormRequest $request,
        SecurityApi $securityApi
    ) {
        $user = $request->findUser();
        $code = $request->code();

        if ($securityApi->reminders()->exists($user, $code)) {
            return view()->make('backoffice::auth.reset-password', [
                'id' => $user->getUserId(),
                'resetCode' => $code,
            ]);
        }

        return redirect()->to(AuthLoginHandler::route())
            ->with('danger', trans('backoffice::auth.validation.reset-password.incorrect'));
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get($backofficePrefix . '/auth/password/reset/{' . self::ROUTE_PARAM_USER . '}/{' . self::ROUTE_PARAM_CODE . '}', self::class)
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
