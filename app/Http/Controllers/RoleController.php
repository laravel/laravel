<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePostRequest;
use App\Models\Role;

class RoleController extends Controller
{
    function createRole(RolePostRequest $role)
    {
        $payload = $role->all();
        $newRole = Role::create($payload);
        return $newRole;
    }
}
