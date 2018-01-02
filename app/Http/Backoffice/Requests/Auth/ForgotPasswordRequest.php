<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Requests\Request;

class ForgotPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function getEmail(): string
    {
        return $this->get('email');
    }
}
