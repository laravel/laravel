<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Requests\Request;

class ResendActivationRequest extends Request
{
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'email' => trans('backoffice::auth.validation.activation.email'),
        ];
    }

    public function getEmail(): string
    {
        return $this->get('email');
    }
}
