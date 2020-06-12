<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;

class UserShowHandler extends Handler implements RouteDefiner
{
    private PermissionParser $permissionParser;

    public function __construct(PermissionParser $permissionParser)
    {
        $this->permissionParser = $permissionParser;
    }

    public function __invoke(UserRequest $request): View
    {
        /** @var User $user */
        $user = $request->findUser();

        $breadcrumb = backoffice()->breadcrumb([
            trans('backoffice::default.home') => DashboardHandler::class,
            trans('backoffice::auth.users') => UserListHandler::class,
            trans('backoffice::auth.user_name', [
                'name' => $user->getName()->getFirstName(),
                'lastname' => $user->getName()->getLastName(),
            ]),
        ]);

        $data = [
            trans('backoffice::auth.first_name') => $user->getName()->getFirstName(),
            trans('backoffice::auth.last_name') => $user->getName()->getLastName(),
            trans('backoffice::auth.email') => $user->getEmail(),
            trans('backoffice::auth.username') => $user->getUsername(),
            trans('backoffice::auth.permissions') => $this->permissionParser->toViewTable(security()->permissions()->all(), $user),
            trans('backoffice::auth.activated') => trans('backoffice::default.' . ($user->isActivated() ? 'yes' : 'no')),
            trans('backoffice::auth.activated_at') => $user->isActivated() ? $user->getActivatedAt()->format(trans('backoffice::default.datetime_format')) : '-',
            trans('backoffice::auth.last_login') => $user->getLastLogin() ? $user->getLastLogin()->format(trans('backoffice::default.datetime_format')) : '-',
        ];

        if ($user instanceof Roleable) {
            /** @var \Doctrine\Common\Collections\Collection $roles */
            $roles = $user->getRoles();

            $data[trans('backoffice::auth.roles')] = implode(', ', $roles->map(function (Role $role): string {
                return $role->getName();
            })->toArray());
        }

        $actions = backoffice()->actions();

        $actions->link(function () use ($user) {
            try {
                return security()->url()->to(UserEditFormHandler::route($user->getUserId()));
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('edit') . ' ' . trans('backoffice::default.edit'),
        [
            'class' => 'btn btn-success',
        ]);

        $actions->link(function () {
            try {
                return security()->url()->to(UserListHandler::route());
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
                return security()->url()->to(UserListHandler::route());
            } catch (SecurityException $e) {
                return false;
            }
        }, fa('arrow-left') . ' ' . trans('backoffice::default.back'));

        return view()->make('backoffice::show', [
            'title' => trans('backoffice::auth.users'),
            'breadcrumb' => $breadcrumb,
            'label' => trans('backoffice::auth.user_name', [
                'name' => $user->getName()->getFirstName(),
                'lastname' => $user->getName()->getLastName(),
            ]),
            'data' => $data,
            'actions' => $actions,
            'topActions' => $topActions,
        ]);
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->get("$backofficePrefix/$routePrefix/{" . UserRequest::ROUTE_PARAM_ID . '}/', [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_READ,
            ])
            ->where(UserRequest::ROUTE_PARAM_ID, '[0-9]+')
            ->name(self::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(int $userId): string
    {
        return route(self::class, [
            UserRequest::ROUTE_PARAM_ID => $userId,
        ]);
    }
}
