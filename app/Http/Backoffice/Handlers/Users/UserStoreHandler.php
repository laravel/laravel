<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Roles\RoleStoreRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Activations\Activation;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Routing\Router;

class UserStoreHandler extends Handler implements RouteDefiner
{
    use SendsEmails;

    public function __invoke(RoleStoreRequest $request)
    {
        $input = $request->all(['firstName', 'lastName', 'email', 'password', 'activated', 'username', 'roles', 'permissions']);

        try {
            /** @var User $user */
            $user = security()->users()->create($input, function (User $user) use ($input) {
                $this->addRoles($user, $input['roles'] ?? []);
            });

            if ($input['activated']) {
                security()->activate($user);
            } else {
                /** @var Activation $activation */
                $activation = security()->activations()->create($user);

                $this->sendActivation(
                    $user,
                    AuthActivateHandler::route($user->getUserId(), $activation->getCode())
                );
            }

            return redirect()->to(url()->to(UserListHandler::route()));
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->getErrors());
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->post("$backofficePrefix/$routePrefix/", [
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

    private function addRoles(User $user, array $roles)
    {
        if ($user instanceof Roleable && ! empty($roles)) {
            /* @var Roleable $user */
            foreach ($roles as $role) {
                /** @var Role $role */
                if ($role = security()->roles()->findBySlug($role)) {
                    $user->addRole($role);
                }
            }
        }
    }
}
