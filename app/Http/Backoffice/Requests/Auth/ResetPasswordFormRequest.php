<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthResetPasswordFormHandler;
use App\Http\Utils\BaseRequest;
use Cartalyst\Sentinel\Users\UserInterface;

class ResetPasswordFormRequest extends BaseRequest
{
    public function findUser(): UserInterface
    {
        /** @var int $id */
        $id = $this->request()->route(AuthResetPasswordFormHandler::ROUTE_PARAM_USER);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function code(): string
    {
        return $this->request()->route(AuthResetPasswordFormHandler::ROUTE_PARAM_CODE);
    }
}
