<?php

namespace App\Domain\Accounts;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Domain\Accounts\Customer;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
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
     * Show the application registration form.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('app/accounts/register', [
            'model' => [
                'action' => route('accounts.store'),
                'email' => $request->session()->get('email'),
                'login_url' => route('session.create'),
            ],
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Domain\Accounts\AccountStoreRequest  $request
     * @return array
     */
    public function store(AccountStoreRequest $request)
    {
        $validated = collect($request->validated())
            ->put('password', Hash::make($request->password))
            ->toArray();

        if (Account::where('email', $validated['email'])->count() === 0) {
            event(new Registered(Account::create($validated)));
        }

        $request->session()
            ->flash('message', trans('accounts.verification.sent'));

        return [ 'redirect' => route('session.create') ];
    }
}
