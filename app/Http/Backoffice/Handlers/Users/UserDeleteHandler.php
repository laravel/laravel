<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use App\Http\Utils\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class UserDeleteHandler extends Handler implements RouteDefiner
{
    public function __invoke(UserRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->findUser();

        try {
            security()->users()->destroy($user);

            return redirect()->to(url()->to(UserListHandler::route()))->withSuccess(
                trans('backoffice::default.delete_msg', ['model' => trans('backoffice::auth.user'), 'id' => $user->getEmail()])
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->delete("$backofficePrefix/$routePrefix/{" . UserRequest::ROUTE_PARAM_ID . '}', [
                'uses' => self::class,
                'permission' => Permission::OPERATOR_DELETE,
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
