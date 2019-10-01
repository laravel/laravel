<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Auth\RoleUpdateRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Illuminate\Routing\Router;

class RoleUpdateHandler extends Handler implements RouteDefiner
{
    public const ROUTE_PARAM_ID = 'role_id';

    public function __invoke(int $roleId, RoleUpdateRequest $request)
    {
        /** @var Role $role */
        $role = security()->roles()->findById($roleId);

        if (! $role) {
            abort(404);
        }

        try {
            $role->setName($request->input('name'));

            if ($role instanceof Permissible) {
                $role->syncPermissions((array) $request->input('permissions'));
            }

            security()->roles()->save($role);

            return redirect()->to(
                security()->url()->to(RoleListHandler::route())
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->put($backofficePrefix . '/' . $routePrefix . '/{' . static::ROUTE_PARAM_ID . '}/', [
                'uses' => static::class,
                'permission' => Permission::ROLE_UPDATE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $roleId)
    {
        return route(static::class, [static::ROUTE_PARAM_ID => $roleId]);
    }
}
