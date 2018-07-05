<?php

namespace App\Domain\Accounts\Verification;

use Carbon\Carbon;
use Hashids\Hashids;

use App\Domain\Accounts\Account;

class VerifyCodeService
{
    /**
     * Creates a VerifyCode that authorises an Account to consent. Will emit
     * VerifyCodeGenerated to rest of app.
     *
     * @param Account  $account
     * @return VerifyCode
     */
    public function createForAccount(Account $account) : VerifyCode
    {
        $account
            ->allVerifyCodes()
            ->delete();

        $hours = config('accounts.verification.code_expiry_hours');

        $verifyCode = $account
            ->verifyCode()
            ->create([ 'expires_at' => Carbon::now()->addHours($hours) ]);

        $salt = $account->created_at->toDateTimeString();
        $minHashLength = 10;

        $hasher = app(Hashids::class, compact('salt', 'minHashLength'));

        $verifyCode->fill([ 'code' => $hasher->encode($verifyCode->id) ]);
        $verifyCode->save();

        event(new VerifyCodeCreatedEvent($verifyCode));

        return $verifyCode;
    }
}
