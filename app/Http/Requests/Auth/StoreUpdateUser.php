<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'name' => 'required|string|min:3|max:190',
            'password' => 'required|min:3|max:14',
            'email' => 'required|email|min:3|max:190',

        ];

        if($this->method() == 'PUT'){
            $rules['password'] = ['nullable|min:3|max:14'];
        }

        return $rules;


    }
}
