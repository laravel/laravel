<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Router;

class RoleShowHandler extends Handler implements RouteDefiner
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

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.roles') => RoleListHandler::class,
            $role->getName(),
        ]);

        $data = [
            trans('backoffice::auth.name') => $role->getName(),
            trans('backoffice::auth.permissions') => $this->permissionParser->toViewTable(
                security()->permissions()->all(),
                $role
            ),
        ];

        $actions = backoffice()->actions();

        try {
            $actions->link(
                security()->url()->to(RoleEditHandler::route($role->getRoleId())),
                fa('edit') . ' ' . trans('backoffice::default.edit'),
                ['class' => 'btn btn-success']
            );
        } catch (SecurityException $e) {
        }

        try {
            $actions->link(
                security()->url()->to(RoleListHandler::route()),
                trans('backoffice::default.back'),
                ['class' => 'btn btn-default']
            );
        } catch (SecurityException $e) {
        }

        $topActions = backoffice()->actions();

        try {
            $topActions->link(
                security()->url()->to(RoleListHandler::route()),
                fa('arrow-left') . ' ' . trans('backoffice::default.back')
            );
        } catch (SecurityException $e) {
        }

        return $view->make('backoffice::show', [
            'title' => trans('backoffice::auth.roles'),
            'breadcrumb' => $breadcrumb,
            'label' => $role->getName(),
            'data' => $data,
            'actions' => $actions,
            'topActions' => $topActions,
        ]);
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/{role_id}/", [
                'uses' => static::class,
                'permission' => Permission::ROLE_READ,
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
