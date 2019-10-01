<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Config\Repository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;

class AuthActivateHandler extends Handler implements RouteDefiner
{
    public function __invoke(
        int $userId,
        string $activationCode,
        SecurityApi $securityApi,
        Repository $config,
        Redirector $redirector,
        Factory $view
    ) {
        $activations = $securityApi->activations();

        /** @var User $user */
        $user = $securityApi->users()->findById($userId) ?: abort(404);

        if ($activations->completed($user)) {
            return $redirector->to(AuthLoginHandler::route())
                ->with('warning', trans('backoffice::auth.validation.user.already-active'));
        }

        if ($activations->exists($user, $activationCode)) {
            $activations->complete($user, $activationCode);

            return $redirector->to(AuthLoginHandler::route())
                ->with('success', trans('backoffice::auth.activation.success'));
        }

        return $view->make('backoffice::auth.activation-expired', [
            'email' => $config->get('backoffice.auth.contact'),
        ]);
    }

    public static function defineRoute(Router $router)
    {
        $router
            ->post(config('backoffice.global_url_prefix') . '/auth/activate/{user_id}/{code}', static::class)
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE_PUBLIC,
            ]);
    }

    public static function route($userId, $code)
    {
        return route(static::class, [
            'user_id' => $userId,
            'code' => $code,
        ]);
    }
}
