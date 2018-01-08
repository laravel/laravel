<?php

namespace App\Http\Backoffice\Handlers\Roles;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleEditRequest;
use App\Http\Backoffice\Requests\Roles\RoleRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Forms\Form;
use Digbang\Backoffice\Support\PermissionParser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class RoleEditFormHandler extends Handler implements RouteDefiner
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

        $form = $this->buildForm(
            security()->url()->to(RoleEditHandler::route($role->getRoleId())),
            trans('backoffice::default.edit') . ' ' . $role->getName(),
            Request::METHOD_PUT,
            security()->url()->to(RoleListHandler::route())
        );

        $permissions = $role->getPermissions()->map(function (\Digbang\Security\Permissions\Permission $permission) {
            return $permission->getName();
        })->toArray();

        $form->fill([
            RoleEditRequest::FIELD_NAME => $role->getName(),
            RoleEditRequest::FIELD_PERMISSIONS . '[]' => $permissions,
        ]);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
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

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.roles.url', 'roles');

        $router
            ->get('/{' . RoleRequest::ROUTE_PARAM_ID . '}/edit', [
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

    private function buildForm($target, $label, $method = Request::METHOD_POST, $cancelAction = '', $options = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs
            ->text(RoleEditRequest::FIELD_NAME, trans('backoffice::auth.name'))
            ->setRequired();

        $inputs->dropdown(
            RoleEditRequest::FIELD_PERMISSIONS,
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray(security()->permissions()->all()),
            [
                'multiple' => 'multiple',
                'class' => 'multiselect',
            ]
        );

        return $form;
    }
}
