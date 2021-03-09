<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordHandler;
use App\Http\Utils\BaseRequest;
use Cartalyst\Sentinel\Users\UserInterface;

class ResetPasswordRequest extends BaseRequest
{
    public function findUser(): UserInterface
    {
        $id = $this->request()->route(AuthResetPasswordHandler::ROUTE_PARAM_USER);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function code(): string
    {
        return $this->request()->route(AuthResetPasswordHandler::ROUTE_PARAM_CODE);
    }

    public function password(): string
    {
        return $this->request()->get('password');
    }

    public function validate(): array
    {
        return $this->request()->validate([
            'password' => 'required|confirmed',
        ], [
            'password' => trans('backoffice::auth.validation.reset-password.confirmation'),
        ]);
    }
}
