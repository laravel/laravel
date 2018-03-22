<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * mje , method validateLogin() overrides validateLogin()  Illuminate/Foundation/Auth/AuthenticatesUsers.php
     *
     * @param array $data
     * @return mixed
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username()      => 'required|string',
            'password'             => 'required|string',
            'g-recaptcha-response' => ['required', new CaptchaRule()],
        ]);

    }
}
