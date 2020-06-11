<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordFormHandler;
use App\Http\Backoffice\Requests\Request;
use Cartalyst\Sentinel\Users\UserInterface;

class ResetPasswordFormRequest extends Request
{
    public function findUser(): UserInterface
    {
        $id = $this->route(AuthResetPasswordFormHandler::ROUTE_PARAM_USER);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function code(): string
    {
        return $this->route(AuthResetPasswordFormHandler::ROUTE_PARAM_CODE);
    }
}
