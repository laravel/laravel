<?php

namespace App\Http\Backoffice\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    use ValidatesUsers {
        rules as defaultRules;
    }

    public function rules()
    {
        $rules = $this->defaultRules();
        $rules['password'] = 'required|' . $rules['password'];

        return $rules;
    }
}
