<?php

namespace App\Http\Backoffice\Handlers\Auth;

use App\Http\Backoffice\Handlers\Handler;
use App\Http\Backoffice\Handlers\SendsEmails;
use App\Http\Backoffice\Requests\Auth\ResendActivationRequest;
use App\Http\Kernel;
use Digbang\Security\Activations\Activation;
use Digbang\Security\Contracts\SecurityApi;
use Digbang\Security\Users\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Router;

class AuthResendActivationHandler extends Handler
{
    use SendsEmails;

    protected const ROUTE_NAME = 'backoffice.auth.resend_activation_request';

    public function __invoke(
        ResendActivationRequest $request,
        SecurityApi $securityApi
    ): RedirectResponse {
        $request->validate();

        $email = $request->email();

        /** @var User|null $user */
        $user = $securityApi->users()->findByCredentials(['email' => $email]);

        if (! $user) {
            redirect()->back()->withInput()->withErrors([
                'email' => trans('backoffice::auth.validation.activation.incorrect', [
                    'email' => $email,
                ]),
            ]);
        }

        $activations = $securityApi->activations();

        /** @var Activation $activation */
        $activation = $activations->exists($user) ?: $activations->create($user);

        $this->sendActivation(
            $user,
            AuthActivateHandler::route($user->getUserId(), $activation->getCode())
        );

        return redirect()->to(AuthLoginHandler::route())->with(
            'success', trans('backoffice::auth.activation.email-sent')
        );
    }

    public static function defineRoute(Router $router): void
    {
        $backofficePrefix = config('backoffice.global_url_prefix');

        $router
            ->post("$backofficePrefix/auth/activate/resend", self::class)
            ->name(self::ROUTE_NAME)
            ->middleware([Kernel::BACKOFFICE_PUBLIC]);
    }

    public static function route(): string
    {
        return route(self::ROUTE_NAME);
    }
}
