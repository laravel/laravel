<?php

namespace App\Http\Backoffice\Requests\Roles;

use Digbang\Security\Roles\DefaultRole;

class RoleEditRequest extends RoleRequest
{
    public const FIELD_NAME = 'name';
    public const FIELD_PERMISSIONS = 'permissions';

    public function rules()
    {
        return [
            'name' => 'required|unique:' . DefaultRole::class . ',name,' . $this->route(static::ROUTE_PARAM_ID),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('backoffice::auth.validation.role.name'),
            'name.unique' => trans('backoffice::auth.validation.role.unique'),
        ];
    }

    public function getName(): string
    {
        return $this->get(static::FIELD_NAME);
    }

    public function getPermissions(): array
    {
        return $this->get(static::FIELD_PERMISSIONS, []) ?? [];
    }
}
