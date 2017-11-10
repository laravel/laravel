<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Backoffice\Requests\Request;
use Digbang\Security\Users\DefaultUser;

class UserStoreRequest extends Request
{
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_EMAIL = 'email';
    public const FIELD_USERNAME = 'username';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_PASSWORD_CONFIRMATION = 'password_confirmation';
    public const FIELD_ACTIVATED = 'activated';
    public const FIELD_ROLES = 'roles';
    public const FIELD_PERMISSIONS = 'permissions';

    public function rules()
    {
        return [
            static::FIELD_FIRST_NAME => 'max:255',
            static::FIELD_LAST_NAME => 'max:255',
            static::FIELD_EMAIL => 'required|email|max:255|unique:' . DefaultUser::class . ',email.address',
            static::FIELD_USERNAME => 'required|alpha|max:255|unique:' . DefaultUser::class . ',username',
            static::FIELD_PASSWORD => 'required|confirmed|min:3',
            static::FIELD_ACTIVATED => 'boolean',
            static::FIELD_ROLES => 'array',
            static::FIELD_PERMISSIONS => 'array',
        ];
    }

    public function messages()
    {
        return [
            static::FIELD_EMAIL . '.required' => trans('backoffice::auth.validation.user.email-required'),
            static::FIELD_EMAIL . '.unique' => trans('backoffice::auth.validation.user.user-email-repeated'),
            static::FIELD_USERNAME . '.required' => trans('backoffice::auth.validation.user.user-username-repeated'),
            static::FIELD_USERNAME . '.unique' => trans('backoffice::auth.validation.user.user-username-repeated'),
            static::FIELD_PASSWORD . '.required' => trans('backoffice::auth.validation.user.password-required'),
        ];
    }

    public function getFirstName(): ?string
    {
        return $this->get(static::FIELD_FIRST_NAME);
    }

    public function getLastName(): ?string
    {
        return $this->get(static::FIELD_LAST_NAME);
    }

    public function getEmail(): string
    {
        return $this->get(static::FIELD_EMAIL);
    }

    public function getUsername(): string
    {
        return $this->get(static::FIELD_USERNAME);
    }

    public function getPassword(): string
    {
        return $this->get(static::FIELD_PASSWORD);
    }

    public function getActivated(): bool
    {
        return $this->get(static::FIELD_ACTIVATED);
    }

    public function getRoles(): array
    {
        return $this->get(static::FIELD_ROLES, []) ?? [];
    }

    public function getPermissions(): array
    {
        return $this->get(static::FIELD_PERMISSIONS, []) ?? [];
    }

    public function getCredentials(): array
    {
        return $this->all([
            static::FIELD_FIRST_NAME,
            static::FIELD_LAST_NAME,
            static::FIELD_EMAIL,
            static::FIELD_USERNAME,
            static::FIELD_PASSWORD,
            static::FIELD_ACTIVATED,
            static::FIELD_ROLES,
            static::FIELD_PERMISSIONS,
        ]);
    }
}
