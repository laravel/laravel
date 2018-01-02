<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Users\User;

class UserRequest extends Request
{
    public const ROUTE_PARAM_ID = 'user_id';

    public function getUser(): User
    {
        $id = $this->route(static::ROUTE_PARAM_ID);
        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }
}
