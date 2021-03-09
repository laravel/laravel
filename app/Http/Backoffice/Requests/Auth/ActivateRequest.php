<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Auth\AuthActivateHandler;
use App\Http\Utils\BaseRequest;
use Digbang\Security\Users\User;

class ActivateRequest extends BaseRequest
{
    public function findUser(): User
    {
        $id = $this->request()->route(AuthActivateHandler::ROUTE_PARAM_USER);

        /** @var User|null $user */
        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }

    public function code(): string
    {
        return $this->request()->route(AuthActivateHandler::ROUTE_PARAM_CODE);
    }
}
