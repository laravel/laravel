<?php

namespace App\Http\Api\Requests\Authentication;

use App\Http\Utils\BaseRequest;

class AuthenticationRequest extends BaseRequest
{
    public const EMAIL = 'email';
    public const PASSWORD = 'password';

    public function credentials(): array
    {
        return [
            self::EMAIL => $this->request()->get(self::EMAIL),
            self::PASSWORD => $this->request()->get(self::PASSWORD),
        ];
    }

    public function validate(): array
    {
        return $this->request()->validate([
            self::EMAIL => 'required|email',
            self::PASSWORD => 'required',
        ]);
    }
}
