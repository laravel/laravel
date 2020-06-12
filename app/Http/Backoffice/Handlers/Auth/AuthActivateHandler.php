<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ActivateRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Config\Repository;
use Illuminate\Routing\Router;

class AuthActivateHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(
        ActivateRequest $request,
        SecurityApi $securityApi,
        Repository $config
    ) {
        $user = $request->findUser();
        $code = $request->code();

        $activations = $securityApi->activations();

        if ($activations->completed($user)) {
            return redirect()->to(AuthLoginHandler::route())
                ->with('warning', trans('backoffice::auth.validation.user.already-active'));
        }

        if ($activations->exists($user, $code)) {
            $activations->complete($user, $code);

            return redirect()->to(AuthLoginHandler::route())
                ->with('success', trans('backoffice::auth.activation.success'));
        }

        return view()->make('backoffice::auth.activation-expired', [
            'email' => $config->get('backoffice.auth.contact'),
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get($backofficePrefix . '/auth/activate/{' . self::ROUTE_PARAM_USER . '}/{' . self::ROUTE_PARAM_CODE . '}', self::class)
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(int $userId, string $code): string
    {
        return route(self::class, [
            self::ROUTE_PARAM_USER => $userId,
            self::ROUTE_PARAM_CODE => $code,
        ]);
    }
}
