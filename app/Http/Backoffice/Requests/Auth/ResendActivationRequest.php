<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Backoffice\Requests\Request;

class ResendActivationRequest extends Request
{
    public function email(): string
    {
        return $this->get('email');
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'email' => trans('backoffice::auth.validation.activation.email'),
        ];
    }
}
