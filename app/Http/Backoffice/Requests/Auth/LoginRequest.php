<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Utils\BaseRequest;

class LoginRequest extends BaseRequest
{
    public function validate(): array
    {
        return $this->request()->validate([
            'email' => 'required_without:login|email',
            'username' => 'required_without:login',
            'login' => 'required_without:email,username',
            'password' => 'required',
        ]);
    }
}
