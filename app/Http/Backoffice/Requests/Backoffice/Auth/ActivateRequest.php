<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Users\User;

class ActivateRequest extends Request
{
    public function getUser(): User
    {
        $id = $this->route(AuthActivateHandler::ROUTE_PARAM_USER);
        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function getCode(): string
    {
        return $this->route(AuthActivateHandler::ROUTE_PARAM_CODE);
    }
}
