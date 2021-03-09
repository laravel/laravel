<?php

namespace App\Http\Backoffice\Requests\Roles;

use App\Http\Utils\BaseRequest;
use Digbang\Security\Roles\DefaultRole;

class RoleCreateRequest extends BaseRequest
{
    public const FIELD_NAME = 'name';
    public const FIELD_SLUG = 'slug';
    public const FIELD_PERMISSIONS = 'permissions';

    public function name(): string
    {
        return $this->request()->get(self::FIELD_NAME);
    }

    public function slug(): ?string
    {
        return $this->request()->get(self::FIELD_SLUG);
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
            self::FIELD_NAME => 'required|unique:' . DefaultRole::class . ',name',
        ], [
            self::FIELD_NAME . '.required' => trans('backoffice::auth.validation.role.name'),
            self::FIELD_NAME . '.unique' => trans('backoffice::auth.validation.role.unique'),
        ]);
    }
}
