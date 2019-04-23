<?php

namespace App\Domain\Accounts\Verification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Domain\Accounts\Account;
use App\Http\Controllers\Controller;
use App\Domain\Accounts\Verification\VerifyCodeService;

class VerifyCodeController extends Controller
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
     * Shows form for requesting additional verify emails.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('app/accounts/resend-verify-code', [
            'model' => [
                'action' => route('verify-codes.create'),
            ],
        ]);
    }

    /**
     * Inbound links for email verification.
     *
     * @param  \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, VerifyCodeService $service)
    {
        $code = $request->route('verify_code') ?: '';
        $token = $request->query('token') ?: '';

        $service->verify($code, $token);

        return redirect(route('session.create'))
            ->with('message', trans('accounts.verification.confirmation'));
    }

    /**
     * Generates a new VerifyCode, triggering email send email.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \App\Domain\Accounts\Verification\VerifyCodeService  $service
     * 
     * @return array
     */
    public function store(Request $request, VerifyCodeService $service)
    {
        $request->validate([ 'email' => 'required|email' ]);

        $account = Account::query()
            ->where('email', $request->email)
            ->first();

        if ($account) {
            $service->create($account);
        }

        return [ 'message' => trans('accounts.verification.resent') ];
    }
}
