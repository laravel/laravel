<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Support\PermissionParser;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Router;

class UserShowHandler extends Handler implements RouteDefiner
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
            /** @var User|Roleable $user */
            $roles = $user->getRoles();

            /* @var \Doctrine\Common\Collections\Collection $roles */
            $data[trans('backoffice::auth.roles')] = implode(', ', $roles->map(function (Role $role) {
                return $role->getName();
            })->toArray());
        }

        $actions = backoffice()->actions();

        try {
            $actions->link(
                security()->url()->to(UserEditFormHandler::route($user->getUserId())),
                fa('edit') . ' ' . trans('backoffice::default.edit'),
                ['class' => 'btn btn-success']
            );
        } catch (SecurityException $e) {
        }

        try {
            $actions->link(
                security()->url()->to(UserListHandler::route()),
                trans('backoffice::default.back'),
                ['class' => 'btn btn-default']
            );
        } catch (SecurityException $e) {
        }

        $topActions = backoffice()->actions();

        try {
            $topActions->link(
                security()->url()->to(UserListHandler::route()),
                fa('arrow-left') . ' ' . trans('backoffice::default.back')
            );
        } catch (SecurityException $e) {
        }

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
            ->get('/{' . UserRequest::ROUTE_PARAM_ID . '}/', [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_READ,
            ])
            ->prefix("$backofficePrefix/$routePrefix")
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
}