<?php

namespace App\Http\Api\Requests\Authentication;

use App\Http\Api\Requests\Request;

class AuthenticationRequest
{
    public const EMAIL = 'email';
    public const PASSWORD = 'password';

    /** @var Request */
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function credentials(): array
    {
        return [
            self::EMAIL => $this->request()->get(self::EMAIL),
            self::PASSWORD => $this->request()->get(self::PASSWORD),
        ];
    }

    public function validate(): array
    {
        $this->request()->validate([
            self::EMAIL => 'required|email',
            self::PASSWORD => 'required',
        ]);
    }
}
