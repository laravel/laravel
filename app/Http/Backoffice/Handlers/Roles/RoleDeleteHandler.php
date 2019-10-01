<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Illuminate\Routing\Router;

class RoleDeleteHandler extends Handler implements RouteDefiner
{
    public function __invoke(int $roleId)
    {
        /** @var Role $role */
        $role = security()->roles()->findById($roleId);

        if (! $role) {
            abort(404);
        }

        try {
            security()->roles()->delete($role);

            return redirect()
                ->to(security()->url()->to(RoleListHandler::route()))
                ->withSuccess(trans('backoffice::default.delete_msg', [
                    'model' => trans('backoffice::auth.role'),
                    'id' => $role->getName(),
                ]));
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->delete("$backofficePrefix/$routePrefix/{role_id}/", [
                'uses' => static::class,
                'permission' => Permission::ROLE_DELETE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $roleId)
    {
        return route(static::class, ['role_id' => $roleId]);
    }
}
