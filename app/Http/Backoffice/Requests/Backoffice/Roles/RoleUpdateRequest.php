<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Handlers\Roles\RoleUpdateHandler;
use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Roles\DefaultRole;

class RoleUpdateRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required|unique:' . DefaultRole::class . ',name,' . $this->route(RoleUpdateHandler::ROUTE_PARAM_ID),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('backoffice::auth.validation.role.name'),
            'name.unique' => trans('backoffice::auth.validation.role.unique'),
        ];
    }
}
