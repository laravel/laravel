<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\User;

class UserController extends Controller
{
    public function createUser(UserFormRequest $request)
    {
        $payload = $request->all();
        $newUser = User::create($payload);
        return $newUser;
    }

}
