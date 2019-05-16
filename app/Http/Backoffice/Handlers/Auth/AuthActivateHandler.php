<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Requests\Auth\ActivateRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthActivateHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_USER = 'user_id';
    public const ROUTE_PARAM_CODE = 'code';

    public function __invoke(
        ActivateRequest $request,
        SecurityApi $securityApi,
        Repository $config,
        Redirector $redirector,
        Factory $view
    ) {
        $user = $request->getUser();
        $code = $request->getCode();

        $activations = $securityApi->activations();

        if ($activations->completed($user)) {
            return $redirector->to(AuthLoginHandler::route())
                ->with('warning', trans('backoffice::auth.validation.user.already-active'));
        }

        if ($activations->exists($user, $code)) {
            $activations->complete($user, $code);

            return $redirector->to(AuthLoginHandler::route())
                ->with('success', trans('backoffice::auth.activation.success'));
        }

        return $view->make('backoffice::auth.activation-expired', [
            'email' => $config->get('backoffice.auth.contact'),
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->get($backofficePrefix . '/auth/activate/{' . static::ROUTE_PARAM_USER . '}/{' . static::ROUTE_PARAM_CODE . '}', static::class)
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(int $userId, string $code): string
    {
        return route(static::class, [
            static::ROUTE_PARAM_USER => $userId,
            static::ROUTE_PARAM_CODE => $code,
        ]);
    }
}
