<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Utils\BaseRequest;
use Digbang\Security\Roles\Role;

class RoleRequest extends BaseRequest
{
    public const ROUTE_PARAM_ID = 'role_id';

    public function getRole(): Role
    {
        /** @var int $id */
        $id = $this->request()->route(self::ROUTE_PARAM_ID);

        /** @var Role|null $role */
        $role = security()->roles()->findById($id);

        if (! $role) {
            abort(404);
        }

        return $role;
    }
}
