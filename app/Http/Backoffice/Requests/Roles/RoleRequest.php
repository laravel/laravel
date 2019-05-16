<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Roles\Role;

class RoleRequest extends Request
{
    public const ROUTE_PARAM_ID = 'role_id';

    public function getRole(): Role
    {
        $id = $this->route(static::ROUTE_PARAM_ID);

        /** @var Role $role */
        $role = security()->roles()->findById($id);

        if (! $role) {
            abort(404);
        }

        return $role;
    }
}
