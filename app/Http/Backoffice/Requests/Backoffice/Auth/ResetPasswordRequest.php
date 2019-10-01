<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Requests\Request;

class ResetPasswordRequest extends Request
{
    public function rules()
    {
        return [
            'password' => 'required|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password' => trans('backoffice::auth.validation.reset-password.confirmation'),
        ];
    }
}
