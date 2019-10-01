<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class UserCreateHandler extends Handler implements RouteDefiner
{
    /** @var PermissionParser */
    private $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(Factory $view)
    {
        $label = trans('backoffice::default.new', ['model' => trans('backoffice::auth.user')]);

        $form = $this->buildForm(
            security()->url()->to(UserStoreHandler::route()),
            $label,
            Request::METHOD_POST,
            security()->url()->to(UserListHandler::route())
        );

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.users') => UserListHandler::class,
            $label,
        ]);

        return $view->make('backoffice::create', [
            'title' => trans('backoffice::auth.users'),
            'form' => $form,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/create", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_CREATE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route()
    {
        return route(static::class);
    }

    private function buildForm($target, $label, $method = Request::METHOD_POST, $cancelAction = '', $options = [])
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs->text('firstName', trans('backoffice::auth.first_name'));
        $inputs->text('lastName', trans('backoffice::auth.last_name'));
        $inputs->text('email', trans('backoffice::auth.email'));
        $inputs->text('username', trans('backoffice::auth.username'));
        $inputs->password('password', trans('backoffice::auth.password'));
        $inputs->password('password_confirmation', trans('backoffice::auth.confirm_password'));
        $inputs->checkbox('activated', trans('backoffice::auth.activated'));

        $roles = security()->roles()->findAll();

        $options = [];
        $rolePermissions = [];
        foreach ($roles as $role) {
            /* @var \Digbang\Security\Roles\Role $role */
            $options[$role->getRoleSlug()] = $role->getName();

            $rolePermissions[$role->getRoleSlug()] = $role->getPermissions()->map(function (\Digbang\Security\Permissions\Permission $permission) {
                return $permission->getName();
            })->toArray();
        }

        $inputs->dropdown(
            'roles',
            trans('backoffice::auth.roles'),
            $options,
            [
                'multiple' => 'multiple',
                'class' => 'user-groups form-control',
                'data-permissions' => json_encode($rolePermissions),
            ]
        );

        $permissions = security()->permissions()->all();

        $inputs->dropdown(
            'permissions',
            trans('backoffice::auth.permissions'),
            $this->permissionParser->toDropdownArray($permissions),
            [
                'multiple' => 'multiple',
                'class' => 'multiselect',
            ]
        );

        return $form;
    }
}
