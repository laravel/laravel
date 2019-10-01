<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Roles\DefaultRole;

class RoleStoreRequest extends Request
{
    public function rules()
    {
        return [
            'name' => 'required|unique:' . DefaultRole::class . ',name',
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
