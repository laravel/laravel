<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleEditRequest;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Permissions\Permissible;
use Illuminate\Routing\Router;

class RoleEditHandler extends Handler implements RouteDefiner
{
    public function __invoke(RoleEditRequest $request)
    {
        $role = $request->getRole();

        try {
            $role->setName($request->getName());

            if ($role instanceof Permissible) {
                $role->syncPermissions($request->getPermissions());
            }

            security()->roles()->save($role);

            return redirect()->to(
                security()->url()->to(RoleListHandler::route())
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->put('/{' . RoleRequest::ROUTE_PARAM_ID . '}', [
                'uses' => static::class,
                'permission' => Permission::ROLE_UPDATE,
            ])
            ->prefix("$backofficePrefix/$routePrefix")
            ->where(RoleRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $roleId): string
    {
        return route(static::class, [
            RoleRequest::ROUTE_PARAM_ID => $roleId,
        ]);
    }
}
