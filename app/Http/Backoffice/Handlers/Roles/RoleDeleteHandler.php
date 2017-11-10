<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Routing\Router;

class RoleDeleteHandler extends Handler implements RouteDefiner
{
    public function __invoke(RoleRequest $request)
    {
        $role = $request->getRole();

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

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->delete("$backofficePrefix/$routePrefix/{" . RoleRequest::ROUTE_PARAM_ID . '}/', [
                'uses' => static::class,
                'permission' => Permission::ROLE_DELETE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $roleId): string
    {
        return route(static::class, [
            RoleRequest::ROUTE_PARAM_ID => $roleId,
        ]);
    }
}
