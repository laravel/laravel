<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Roles\DefaultRole;

class RoleCreateRequest extends Request
{
    public const FIELD_NAME = 'name';
    public const FIELD_SLUG = 'slug';
    public const FIELD_PERMISSIONS = 'permissions';

    public function name(): string
    {
        return $this->get(self::FIELD_NAME);
    }

    public function slug(): ?string
    {
        return $this->get(self::FIELD_SLUG);
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
            self::FIELD_NAME => 'required|unique:' . DefaultRole::class . ',name',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            self::FIELD_NAME . '.required' => trans('backoffice::auth.validation.role.name'),
            self::FIELD_NAME . '.unique' => trans('backoffice::auth.validation.role.unique'),
        ];
    }
}
