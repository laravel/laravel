<?php

namespace App\Domain\Accounts;

trait AccountRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function accountRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
