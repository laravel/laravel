<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Backoffice\Requests\Request;

class UserRequest extends Request
{
    public const ROUTE_PARAM_ID = 'user_id';

    public function getUserById()
    {
        $id = $this->route(static::ROUTE_PARAM_ID);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }
}
