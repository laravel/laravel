<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;


class UserAPIController extends Controller
{
    public function getAllUsers() {
        $users = User::all();
        return response($users, 200);
    }

    public function createUser(Request $request) {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required'
        ]);
        $user = User::create($request->all());

        return response($user, 201);
    }
}
