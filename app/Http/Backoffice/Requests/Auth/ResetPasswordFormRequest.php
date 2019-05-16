<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordFormHandler;
use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Users\User;

class ResetPasswordFormRequest extends Request
{
    public function getUser(): User
    {
        $id = $this->route(AuthResetPasswordFormHandler::ROUTE_PARAM_USER);

        /** @var User $user */
        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function getCode(): string
    {
        return $this->route(AuthResetPasswordFormHandler::ROUTE_PARAM_CODE);
    }
}
