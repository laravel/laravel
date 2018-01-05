<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
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

    public function __invoke(RoleRequest $request, Factory $view)
    {
        $role = $request->getRole();

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
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
                security()->url()->to(RoleEditFormHandler::route($role->getRoleId())),
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

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get("$backofficePrefix/$routePrefix/{" . RoleRequest::ROUTE_PARAM_ID . '}/', [
                'uses' => static::class,
                'permission' => Permission::ROLE_READ,
            ])
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
