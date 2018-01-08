<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Backoffice\Requests\Users\UserRequest;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Illuminate\Routing\Router;

class UserResetPasswordHandler extends Handler implements RouteDefiner
{
    use SendsEmails;

    public function __invoke(UserRequest $request)
    {
        $user = $request->getUser();

        try {
            /** @var \Digbang\Security\Reminders\Reminder $reminder */
            $reminder = security()->reminders()->create($user);

            $this->sendPasswordReset(
                $user,
                security()->url()->to(AuthResetPasswordHandler::route($user->getUserId(), $reminder->getCode()))
            );

            return redirect()->back()->withSuccess(trans('backoffice::auth.reset-password.email-sent', ['email' => $user->getEmail()]));
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
            ->post('/{' . UserRequest::ROUTE_PARAM_ID . '}/reset-password', [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_RESET_PASSWORD,
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
