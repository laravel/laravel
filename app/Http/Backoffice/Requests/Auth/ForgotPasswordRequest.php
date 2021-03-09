<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Utils\BaseRequest;

class ForgotPasswordRequest extends BaseRequest
{
    public function validate(): array
    {
        return $this->request()->validate([
            'email' => 'required|email',
        ]);
    }

    public function getEmail(): string
    {
        return $this->request()->get('email');
    }
}
