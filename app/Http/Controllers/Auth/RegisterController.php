<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User as UserModel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => 'required|string|max:255'],
            'password' => ['required', 'string', 'min:8', 'max:12', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return UserModel::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => md5($data['password']),
        ]);
    }
}
