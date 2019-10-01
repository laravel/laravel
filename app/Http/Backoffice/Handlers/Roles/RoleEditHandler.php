<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Roles\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class RoleEditHandler extends Handler implements RouteDefiner
{
    /** @var PermissionParser */
    private $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(int $roleId, Factory $view)
    {
        /** @var Role $role */
        $role = security()->roles()->findById($roleId);

        if (! $role) {
            abort(404);
        }

        $form = $this->buildForm(
            security()->url()->to(RoleUpdateHandler::route($role->getRoleId())),
            trans('backoffice::default.edit') . ' ' . $role->getName(),
            Request::METHOD_PUT,
            security()->url()->to(RoleListHandler::route())
        );

        $permissions = $role->getPermissions()->map(function (\Digbang\Security\Permissions\Permission $permission) {
            return $permission->getName();
        })->toArray();

        $form->fill([
            'name' => $role->getName(),
            'permissions[]' => $permissions,
        ]);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.roles') => RoleListHandler::class,
            $role->getName() => RoleShowHandler::route($role->getRoleId()),
            trans('backoffice::default.edit'),
        ]);

        return $view->make('backoffice::edit', [
            'title' => trans('backoffice::auth.roles'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/{role_id}/edit", [
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
        return route(static::class, ['role_id' => $roleId]);
    }

    private function buildForm($target, $label, $method = Request::METHOD_POST, $cancelAction = '', $options = [])
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs->text('name', trans('backoffice::auth.name'));
        $inputs->dropdown(
            'permissions',
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray(security()->permissions()->all()),
            ['multiple' => 'multiple', 'class' => 'multiselect']
        );

        return $form;
    }
}
