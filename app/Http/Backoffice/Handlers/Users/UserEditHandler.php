<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class UserEditHandler extends Handler implements RouteDefiner
{
    /** @var PermissionParser */
    private $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(int $userId, Factory $view)
    {
        /** @var User $user */
        $user = security()->users()->findById($userId);

        if (! $user) {
            abort(404);
        }

        $form = $this->buildForm(
            security()->url()->to(UserUpdateHandler::route($user->getUserId())),
            trans('backoffice::default.edit') . ' ' . trans('backoffice::auth.user_name', ['name' => $user->getName()->getFirstName(), 'lastname' => $user->getName()->getLastName()]),
            Request::METHOD_PUT,
            security()->url()->to(UserListHandler::route())[],
            $user
        );

        $data = [
            'firstName' => $user->getName()->getFirstName(),
            'lastName' => $user->getName()->getLastName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
        ];

        /** @var User|Roleable|Permissible $user */
        if ($user instanceof Roleable) {
            $roles = $user->getRoles();

            /* @var \Doctrine\Common\Collections\Collection $roles */
            $data['roles[]'] = $roles->map(function (Role $role) {
                return $role->getRoleSlug();
            })->toArray();
        }

        if ($user instanceof Permissible) {
            $data['permissions[]'] = [];
            foreach (security()->permissions()->all() as $permission) {
                if ($user->hasAccess($permission)) {
                    $data['permissions[]'][] = (string) $permission;
                }
            }
        }

        $form->fill($data);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardIndexHandler::class,
            trans('backoffice::auth.users') => UserListHandler::class,
            trans('backoffice::auth.user_name', [
                'name' => $user->getName()->getFirstName(),
                'lastname' => $user->getName()->getLastName(),
            ]) => UserShowHandler::route($user->getUserId()),
            trans('backoffice::default.edit'),
        ]);

        return $view->make('backoffice::edit', [
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
            ->get("$backofficePrefix/$routePrefix/{user_id}/edit", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_UPDATE,
            ])
            ->name(static::class)
            ->middleware([
                Kernel::WEB,
                Kernel::BACKOFFICE,
            ]);
    }

    public static function route(int $userId)
    {
        return route(static::class, ['user_id' => $userId]);
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
