<?php

namespace App\Domain\Accounts;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app/accounts/register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \App\Domain\Accounts\AccountStoreRequest  $request
     * @return \Illuminate\Http\Response
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
