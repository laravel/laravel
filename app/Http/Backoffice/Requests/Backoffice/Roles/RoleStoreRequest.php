<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Roles\DefaultRole;

class RoleStoreRequest extends Request
{
    public const FIELD_NAME = 'name';
    public const FIELD_SLUG = 'slug';
    public const FIELD_PERMISSIONS = 'permissions';

    public function rules()
    {
        return [
            static::FIELD_NAME => 'required|unique:' . DefaultRole::class . ',name',
        ];
    }

    public function messages()
    {
        return [
            static::FIELD_NAME . '.required' => trans('backoffice::auth.validation.role.name'),
            static::FIELD_NAME . '.unique' => trans('backoffice::auth.validation.role.unique'),
        ];
    }

    public function getName(): string
    {
        return $this->get(static::FIELD_NAME);
    }

    public function getSlug(): ?string
    {
        return $this->get(static::FIELD_SLUG);
    }

    public function getPermissions(): array
    {
        return $this->get(static::FIELD_PERMISSIONS, []) ?? [];
    }
}
