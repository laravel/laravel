<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;

class RoleShowHandler extends Handler implements RouteDefiner
{
    private PermissionParser $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(RoleRequest $request): View
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

        $actions->link(function () use ($role) {
            try {
                return security()->url()->to(RoleEditFormHandler::route($role->getRoleId()));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('edit') . ' ' . trans('backoffice::default.edit'),
        [
            'class' => 'btn btn-success',
        ]);

        $actions->link(function () {
            try {
                return security()->url()->to(RoleListHandler::route());
            } catch (SecurityException $e) {
                return false;
            }
        }, trans('backoffice::default.back'),
        [
            'class' => 'btn btn-default',
        ]);

        $topActions = backoffice()->actions();

        $topActions->link(function () {
            try {
                return security()->url()->to(RoleListHandler::route());
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('arrow-left') . ' ' . trans('backoffice::default.back'));

        return view()->make('backoffice::show', [
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
            ->get("$backofficePrefix/$routePrefix/{" . RoleRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::ROLE_READ,
            ])
            ->where(RoleRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $roleId): string
    {
        return route(self::class, [
            RoleRequest::ROUTE_PARAM_ID => $roleId,
        ]);
    }
}
