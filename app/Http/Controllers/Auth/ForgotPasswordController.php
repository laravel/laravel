<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * mje method: validateEmail(Request $request)                      overrides
     * Illuminate\Foundation\Auth\SendsPasswordResetEmails.php  validateEmail(Request $request)
     * @param Request $request
     */
    protected function validateEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email' , 'g-recaptcha-response' => 'required', new CaptchaRule()] );


    }
}
