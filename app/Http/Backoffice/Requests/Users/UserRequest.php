<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Utils\BaseRequest;
use Cartalyst\Sentinel\Users\UserInterface;

class UserRequest extends BaseRequest
{
    public const ROUTE_PARAM_ID = 'user_id';

    public function findUser(): UserInterface
    {
        /** @var int $id */
        $id = $this->request()->route(self::ROUTE_PARAM_ID);

        $user = security()->users()->findById($id);

        if (! $user) {
            abort(404);
        }

        return $user;
    }
}
