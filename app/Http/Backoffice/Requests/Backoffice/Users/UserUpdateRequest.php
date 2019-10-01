<?php

namespace App\Http\Backoffice\Requests\Users;

use App\Http\Backoffice\Handlers\Users\UserUpdateHandler;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    use ValidatesUsers {
        rules as defaultRules;
    }

    /**
     * Exclude this user from the unique validation.
     *
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = $this->defaultRules();

        $userId = $this->route(UserUpdateHandler::ROUTE_PARAM_ID);

        $rules['email'] .= ',' . $userId;
        $rules['username'] .= ',' . $userId;
        $rules['password'] = 'nullable|' . $rules['password'];

        return $rules;
    }
}
