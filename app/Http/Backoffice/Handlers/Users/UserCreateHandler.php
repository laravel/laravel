<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserCreateRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Activations\Activation;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Roles\Role;
use Digbang\Security\Roles\Roleable;
use Digbang\Security\Users\User;
use Illuminate\Routing\Router;

class UserCreateHandler extends Handler implements RouteDefiner
{
    use SendsEmails;

    public function __invoke(UserCreateRequest $request)
    {
        try {
            /** @var User $user */
            $user = security()->users()->create($request->getCredentials(), function (User $user) use ($request) {
                $this->addRoles($user, $request->getRoles());
            });

            if ($request->getActivated()) {
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
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->post('/', [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_CREATE,
            ])
            ->prefix("$backofficePrefix/$routePrefix")
            ->name(static::class)
            ->middleware([Kernel::BACKOFFICE]);
    }

    public static function route(): string
    {
        return route(static::class);
    }

    private function addRoles(User $user, array $roles): void
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
