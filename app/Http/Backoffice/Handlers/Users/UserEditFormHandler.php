<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserEditRequest;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Backoffice\Forms\Form;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Permissions\Permissible;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class UserEditFormHandler extends Handler implements RouteDefiner
{
    /** @var PermissionParser */
    private $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(UserRequest $request, Factory $view)
    {
        $user = $request->getUser();

        $form = $this->buildForm(
            security()->url()->to(UserEditHandler::route($user->getUserId())),
            trans('backoffice::default.edit') . ' ' . trans('backoffice::auth.user_name', ['name' => $user->getName()->getFirstName(), 'lastname' => $user->getName()->getLastName()]),
            Request::METHOD_PUT,
            security()->url()->to(UserListHandler::route())
        );

        $data = [
            UserEditRequest::FIELD_FIRST_NAME => $user->getName()->getFirstName(),
            UserEditRequest::FIELD_LAST_NAME => $user->getName()->getLastName(),
            UserEditRequest::FIELD_EMAIL => $user->getEmail(),
            UserEditRequest::FIELD_USERNAME => $user->getUsername(),
        ];

        if ($user instanceof Roleable) {
            /* @var \Doctrine\Common\Collections\Collection $roles */
            $roles = $user->getRoles();

            $data[UserEditRequest::FIELD_ROLES . '[]'] = $roles->map(function (Role $role) {
                return $role->getRoleSlug();
            })->toArray();
        }

        if ($user instanceof Permissible) {
            $data[UserEditRequest::FIELD_PERMISSIONS . '[]'] = [];
            foreach (security()->permissions()->all() as $permission) {
                if ($user->hasAccess($permission)) {
                    $data[UserEditRequest::FIELD_PERMISSIONS . '[]'][] = (string) $permission;
                }
            }
        }

        $form->fill($data);

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
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

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/{" . UserRequest::ROUTE_PARAM_ID . '}/edit', [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_UPDATE,
            ])
            ->where(UserRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $userId): string
    {
        return route(static::class, [
            UserRequest::ROUTE_PARAM_ID => $userId,
        ]);
    }

    private function buildForm($target, $label, $method = Request::METHOD_POST, $cancelAction = '', $options = []): Form
    {
        $form = backoffice()->form($target, $label, $method, $cancelAction, $options);

        $inputs = $form->inputs();

        $inputs
            ->text(UserEditRequest::FIELD_FIRST_NAME, trans('backoffice::auth.first_name'))
            ->setRequired();

        $inputs
            ->text(UserEditRequest::FIELD_LAST_NAME, trans('backoffice::auth.last_name'))
            ->setRequired();

        $inputs
            ->text(UserEditRequest::FIELD_EMAIL, trans('backoffice::auth.email'))
            ->setRequired();

        $inputs
            ->text(UserEditRequest::FIELD_USERNAME, trans('backoffice::auth.username'))
            ->setRequired();

        $inputs->password(UserEditRequest::FIELD_PASSWORD, trans('backoffice::auth.password'));
        $inputs->password(UserEditRequest::FIELD_PASSWORD_CONFIRMATION, trans('backoffice::auth.confirm_password'));

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
            UserEditRequest::FIELD_ROLES,
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
            UserEditRequest::FIELD_PERMISSIONS,
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
