<?php

namespace App\Http\Backoffice\Requests\Users;

use Digbang\Security\Users\DefaultUser;

trait ValidatesUsers
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'firstName' => 'max:255',
            'lastName' => 'max:255',
            'activated' => 'boolean',
            'email' => 'required|email|max:255|unique:' . DefaultUser::class . ',email.address',
            'password' => 'required|confirmed|min:3',
            'username' => 'required|alpha|max:255|unique:' . DefaultUser::class . ',username',
            'roles' => 'array',
            'permissions' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => trans('backoffice::auth.validation.user.email-required'),
            'email.unique' => trans('backoffice::auth.validation.user.user-email-repeated'),
            'username.required' => trans('backoffice::auth.validation.user.user-username-repeated'),
            'username.unique' => trans('backoffice::auth.validation.user.user-username-repeated'),
            'password.required' => trans('backoffice::auth.validation.user.password-required'),
        ];
    }
}
