<?php

namespace App\Http\Api\Requests\Users;

use App\Http\Utils\BaseRequest;
use ProjectName\Entities\User;
use ProjectName\Payloads\UserPayload;

class UserCreateRequest extends BaseRequest implements UserPayload
{
    public const FIELD_FIRST_NAME = 'firstName';
    public const FIELD_LAST_NAME = 'lastName';
    public const FIELD_EMAIL = 'email';
    public const FIELD_PASSWORD = 'password';

    public function firstName(): string
    {
        return $this->request()->get(self::FIELD_FIRST_NAME);
    }

    public function lastName(): string
    {
        return $this->request()->get(self::FIELD_LAST_NAME);
    }

    public function email(): string
    {
        return $this->request()->get(self::FIELD_EMAIL);
    }

    public function password(): string
    {
        return $this->request()->get(self::FIELD_PASSWORD);
    }

    public function validate(): array
    {
        return $this->request()->validate([
            self::FIELD_FIRST_NAME => 'required|max:255',
            self::FIELD_LAST_NAME => 'required|max:255',
            self::FIELD_EMAIL => 'required|email|max:255|unique:' . User::class . ',email',
            self::FIELD_PASSWORD => 'required|min:8',
        ]);
    }
}
