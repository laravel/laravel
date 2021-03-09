<?php

namespace App\Http\Backoffice\Requests\Users;

use Digbang\Security\Users\DefaultUser;

class UserEditRequest extends UserRequest
{
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_EMAIL = 'email';
    public const FIELD_USERNAME = 'username';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_PASSWORD_CONFIRMATION = 'password_confirmation';
    public const FIELD_ROLES = 'roles';
    public const FIELD_PERMISSIONS = 'permissions';

    public function firstName(): ?string
    {
        return $this->request()->get(self::FIELD_FIRST_NAME);
    }

    public function lastName(): ?string
    {
        return $this->request()->get(self::FIELD_LAST_NAME);
    }

    public function email(): string
    {
        return $this->request()->get(self::FIELD_EMAIL);
    }

    public function username(): string
    {
        return $this->request()->get(self::FIELD_USERNAME);
    }

    public function password(): string
    {
        return $this->request()->get(self::FIELD_PASSWORD);
    }

    /**
     * @return string[]
     */
    public function roles(): array
    {
        return $this->request()->get(self::FIELD_ROLES, []) ?? [];
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return $this->request()->get(self::FIELD_PERMISSIONS, []) ?? [];
    }

    public function credentials(): array
    {
        return $this->request()->all([
            self::FIELD_FIRST_NAME,
            self::FIELD_LAST_NAME,
            self::FIELD_EMAIL,
            self::FIELD_USERNAME,
            self::FIELD_PASSWORD,
            self::FIELD_ROLES,
            self::FIELD_PERMISSIONS,
        ]);
    }

    public function validate(): array
    {
        $userId = $this->request()->route(self::ROUTE_PARAM_ID);

        return $this->request()->validate([
            self::FIELD_FIRST_NAME => 'max:255',
            self::FIELD_LAST_NAME => 'max:255',
            self::FIELD_EMAIL => 'required|email|max:255|unique:' . DefaultUser::class . ',email.address,' . $userId,
            self::FIELD_USERNAME => 'required|alpha|max:255|unique:' . DefaultUser::class . ',username,' . $userId,
            self::FIELD_PASSWORD => 'nullable|confirmed|min:3',
            self::FIELD_ROLES => 'array',
            self::FIELD_PERMISSIONS => 'array',
        ], [
            self::FIELD_EMAIL . '.required' => trans('backoffice::auth.validation.user.email-required'),
            self::FIELD_EMAIL . '.unique' => trans('backoffice::auth.validation.user.user-email-repeated'),
            self::FIELD_USERNAME . '.required' => trans('backoffice::auth.validation.user.user-username-repeated'),
            self::FIELD_USERNAME . '.unique' => trans('backoffice::auth.validation.user.user-username-repeated'),
            self::FIELD_PASSWORD . '.required' => trans('backoffice::auth.validation.user.password-required'),
        ]);
    }
}
