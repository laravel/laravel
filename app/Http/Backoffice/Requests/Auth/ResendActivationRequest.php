<?php

namespace App\Http\Backoffice\Requests\Auth;

use App\Http\Utils\BaseRequest;

class ResendActivationRequest extends BaseRequest
{
    public function email(): string
    {
        return $this->request()->get('email');
    }

    public function validate(): array
    {
        return $this->request()->validate([
            'email' => 'required|email',
        ], [
            'email' => trans('backoffice::auth.validation.activation.email'),
        ]);
    }
}
