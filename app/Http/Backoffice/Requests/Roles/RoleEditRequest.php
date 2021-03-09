<?php

namespace App\Http\Backoffice\Requests\Roles;

use Digbang\Security\Roles\DefaultRole;

class RoleEditRequest extends RoleRequest
{
    public const FIELD_NAME = 'name';
    public const FIELD_PERMISSIONS = 'permissions';

    public function name(): string
    {
        return $this->request()->get(self::FIELD_NAME);
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->request()->get(self::FIELD_PERMISSIONS, []) ?? [];
    }

    public function validate(): array
    {
        return $this->request()->validate([
            'name' => 'required|unique:' . DefaultRole::class . ',name,' . $this->request()->route(self::ROUTE_PARAM_ID),
        ], [
            'name.required' => trans('backoffice::auth.validation.role.name'),
            'name.unique' => trans('backoffice::auth.validation.role.unique'),
        ]);
    }
}
