<?php

namespace App\Domain\Accounts;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class SessionController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('destroy');

        $this->middleware(function (Request $request, $next) {
            $request->session()->flash('email', $request->email);
            return $next($request);
        })->only('store');
    }

    /**
     * Display the form to login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('app/accounts/login', [
            'model' => [
                'action' => route('session.store'),
                'register_url' => route('accounts.create'),
                'forgot_password_url' => route('password-resets.create'),
            ],
        ]);
    }

    /**
     * Creates a new session, logging in the Account if credentials match.
     *
     * @param  SessionStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SessionStoreRequest $request)
    {
        return $this->login($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return [ 'redirect' => route('home.show') ];
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $request  Request
     * @param  $user  Account
     * @return array
     */
    protected function authenticated(Request $request, $user)
    {
        return [ 'redirect' => $request->session()->pull('url.intended', route('home.show')) ];
    }
}
