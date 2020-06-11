<?php

namespace App\Http\Backoffice\Requests\Roles;

use Digbang\Security\Roles\DefaultRole;

class RoleEditRequest extends RoleRequest
{
    public const FIELD_NAME = 'name';
    public const FIELD_PERMISSIONS = 'permissions';

    public function name(): string
    {
        return $this->get(self::FIELD_NAME);
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->get(self::FIELD_PERMISSIONS, []) ?? [];
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:' . DefaultRole::class . ',name,' . $this->route(self::ROUTE_PARAM_ID),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => trans('backoffice::auth.validation.role.name'),
            'name.unique' => trans('backoffice::auth.validation.role.unique'),
        ];
    }
}
