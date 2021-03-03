<?php

namespace App\Http\Api\Requests\Authentication;

use App\Http\Api\Requests\Request;

class AuthenticationRequest extends Request
{
    public const EMAIL = 'email';
    public const PASSWORD = 'password';

    public function credentials(): array
    {
        return [
            self::EMAIL => $this->get(self::EMAIL),
            self::PASSWORD => $this->get(self::PASSWORD),
        ];
    }

    public function rules(): array
    {
        return [
            self::EMAIL => 'required|email',
            self::PASSWORD => 'required',
        ];
    }
}
