<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserUpdateRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Routing\Router;

class UserUpdateHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_ID = 'user_id';

    public function __invoke(int $userId, UserUpdateRequest $request)
    {
        /** @var User $user */
        $user = security()->users()->findById($userId);

        if (! $user) {
            abort(404);
        }

        try {
            security()->users()->update($user, $request->all([
                'firstName',
                'lastName',
                'email',
                'username',
                'password',
                'activated',
                'roles',
                'permissions',
            ]));

            return redirect()->to(url()->to(UserListHandler::route()));
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->put($backofficePrefix . '/' . $routePrefix . '/{' . static::ROUTE_PARAM_ID . '}/', [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_UPDATE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $userId)
    {
        return route(static::class, [static::ROUTE_PARAM_ID => $userId]);
    }
}
