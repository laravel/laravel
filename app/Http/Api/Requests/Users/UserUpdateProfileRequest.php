<?php

namespace App\Http\Api\Requests\Users;

use App\Http\Utils\BaseRequest;
use ProjectName\Entities\User;
use ProjectName\Payloads\UserUpdatePayload;

class UserUpdateProfileRequest extends BaseRequest implements UserUpdatePayload
{
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_EMAIL = 'email';
    public const FIELD_PASSWORD = 'password';

    public function firstName(): ?string
    {
        return $this->request()->get(self::FIELD_FIRST_NAME);
    }

    public function lastName(): ?string
    {
        return $this->request()->get(self::FIELD_LAST_NAME);
    }

    public function email(): ?string
    {
        return $this->request()->get(self::FIELD_EMAIL);
    }

    public function password(): ?string
    {
        return $this->request()->get(self::FIELD_PASSWORD);
    }

    public function validate(User $user): array
    {
        return $this->request()->validate([
            self::FIELD_FIRST_NAME => 'required_with:' . self::FIELD_LAST_NAME . '|max:255',
            self::FIELD_LAST_NAME => 'required_with:' . self::FIELD_FIRST_NAME . '|max:255',
            self::FIELD_EMAIL => 'sometimes|nullable|email|max:255|unique:' . User::class . ',email,' . $user->getId(),
            self::FIELD_PASSWORD => 'sometimes|nullable|min:8',
        ]);
    }
}
