<?php

namespace App\Http\Backoffice\Handlers\Users;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordRequestHandler;
use App\Http\Backoffice\Handlers\Dashboard\DashboardIndexHandler;
use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Permission;
use App\Http\Kernel;
use App\Http\Util\RouteDefiner;
use Digbang\Backoffice\Exceptions\ValidationException;
use Digbang\Security\Exceptions\SecurityException;
use Digbang\Security\Users\User;
use Illuminate\Routing\Router;

class UserResetPasswordHandler extends Handler implements RouteDefiner
{
    use SendsEmails;

    public function __invoke(int $userId)
    {
        /** @var User $user */
        $user = security()->users()->findById($userId);

        if (! $user) {
            abort(404);
        }

        try {
            /** @var \Digbang\Security\Reminders\Reminder $reminder */
            $reminder = security()->reminders()->create($user);

            $this->sendPasswordReset(
                $user,
                security()->url()->to(AuthResetPasswordRequestHandler::route($user->getUserId(), $reminder->getCode()))
            );

            return redirect()->back()->withSuccess(trans('backoffice::auth.reset-password.email-sent', ['email' => $user->getEmail()]));
        } catch (ValidationException $e) {
            return redirect()->back()->withDanger(implode('<br/>', $e->getErrors()));
        } catch (SecurityException $e) {
            return redirect()->to(url()->to(DashboardIndexHandler::route()))->withDanger(trans('backoffice::auth.permission_error'));
        }
    }

    public static function defineRoute(Router $router)
    {
        $backofficePrefix = config('backoffice.global_url_prefix');
        $routePrefix = config('backoffice.auth.users.url', 'operators');

        $router
            ->delete("$backofficePrefix/$routePrefix/{user_id}/reset-password", [
                'uses' => static::class,
                'permission' => Permission::OPERATOR_RESET_PASSWORD,
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
}
