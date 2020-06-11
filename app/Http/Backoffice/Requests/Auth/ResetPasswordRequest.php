<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordHandler;
use App\Http\Backoffice\Requests\Request;
use Cartalyst\Sentinel\Users\UserInterface;

class ResetPasswordRequest extends Request
{
    public function findUser(): UserInterface
    {
        $id = $this->route(AuthResetPasswordHandler::ROUTE_PARAM_USER);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function code(): string
    {
        return $this->route(AuthResetPasswordHandler::ROUTE_PARAM_CODE);
    }

    public function password(): string
    {
        return $this->get('password');
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'password' => 'required|confirmed',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'password' => trans('backoffice::auth.validation.reset-password.confirmation'),
        ];
    }
}
