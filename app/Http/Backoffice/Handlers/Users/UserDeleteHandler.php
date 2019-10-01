<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Routing\Router;

class UserDeleteHandler extends Handler implements RouteDefiner
{
    public function __invoke(int $userId)
    {
        /** @var User $user */
        $user = security()->users()->findById($userId);

        if (! $user) {
            abort(404);
        }

        try {
            security()->users()->destroy($user);

            return redirect()->to(url()->to(UserListHandler::route()))->withSuccess(
                trans('backoffice::default.delete_msg', ['model' => trans('backoffice::auth.user'), 'id' => $user->getEmail()])
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->delete("$backofficePrefix/$routePrefix/{user_id}/", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_DELETE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $userId)
    {
        return route(static::class, ['user_id' => $userId]);
    }
}
