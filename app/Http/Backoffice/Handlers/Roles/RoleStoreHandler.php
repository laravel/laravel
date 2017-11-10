<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleStoreRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Illuminate\Routing\Router;

class RoleStoreHandler extends Handler implements RouteDefiner
{
    public function __invoke(RoleStoreRequest $request)
    {
        try {
            $roles = security()->roles();

            /** @var Role|Permissible $role */
            $role = $roles->create($request->getName(), $request->getSlug());

            if ($request->getPermissions() && $role instanceof Permissible) {
                foreach ($request->getPermissions() as $permission) {
                    $role->addPermission($permission);
                }

                $roles->save($role);
            }

            return redirect()->to(
                security()->url()->route(RoleListHandler::class)
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->post("$backofficePrefix/$routePrefix/", [
                'uses' => static::class,
                'permission' => Permission::ROLE_CREATE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(): string
    {
        return route(static::class);
    }
}
