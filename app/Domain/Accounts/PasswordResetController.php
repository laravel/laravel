<?php

namespace App\Domain\Accounts;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    use AccountRules;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('destroy');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('app/accounts/forgot-password', [
            'model' => [
                'action' => route('password-resets.store'),
                'email' => $request->session()->get('email'),
                'login_url' => route('session.create'),
                'register_url' => route('accounts.create'),
            ],
        ]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $this->broker()->sendResetLink(
            $request->only('email')
        );

        return [ 'message' => trans('accounts.passwords.sent') ];
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return view('app/accounts/password-reset', [
            'model' => [
                'action' => route('password-resets.update', $request->token),
                'token' => $request->token,
            ],
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => array_get($this->accountRules(), 'password'),
        ]);

        $all = $request->all();
        $token = $request->route('password_reset');

        $response = $this->broker()->reset(array_merge(compact('token'), $all), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));

            $this->guard()->login($user);
        });

        if ($response !== Password::PASSWORD_RESET) {
            return response()->json([ 'message' => trans('accounts.' . $response) ], 422);
        }

        $request->session()->flash('message', trans('accounts.' . $response));

        return [ 'redirect' => route('session.create') ];
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
