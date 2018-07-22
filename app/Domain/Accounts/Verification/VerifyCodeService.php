<?php

namespace App\Domain\Accounts\Verification;

use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Domain\Accounts\Account;

class VerifyCodeService
{
    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @return  void
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * Creates a VerifyCode that authorises an Account to consent. Will emit
     * created event to rest of app and notify the Account directly.
     *
     * @param Account  $account
     * @return VerifyCode
     */
    public function create(Account $account) : VerifyCode
    {
        return call_user_func_array(function (VerifyCode $verifyCode, string $token) use ($account) {
            event(new Events\VerifyCodeCreated($verifyCode));

            $account->notify(new VerifyCodeNotification($verifyCode->code, $token));

            return $verifyCode;
        }, $this->createVerifyCode($account));
    }

    /**
     * Verifies account for token, if it exists. VerifyCode will be deleted if
     * it exists.
     *
     * @param  string  $code
     * @param  string  $token
     * @return void
     */
    public function verify(string $code, string $token)
    {
        $verifyCode = $this->attemptVerify($code, $token);

        if (is_null($verifyCode)) {
            return;
        }

        event(new Events\AccountVerified($verifyCode->account));
    }

    /**
     * Create a new random token for the email.
     *
     * @param  $email  string
     * @return string
     */
    protected function newToken(string $email) : string
    {
        return hash_hmac('sha256', Str::random(40), $email);
    }

    /**
     * Returns date when the new token should expire.
     *
     * @return Carbon
     */
    protected function newExpiry() : Carbon
    {
        return now()->addHours(config('accounts.verification.code_expiry_hours'));
    }

    /**
     * Within a transaction, deletes all previous verify codes and creates a
     * new one for the Account, returning the VerifyCode and token in an array.
     *
     * @param  $account  Account
     * @return array
     */
    protected function createVerifyCode(Account $account) : array
    {
        return DB::transaction(function () use ($account) {
            $account
                ->allVerifyCodes()
                ->delete();

            $token = $this->newToken($account->email);

            $verifyCode = $account
                ->verifyCode()
                ->create([
                    'token' => $this->hasher->make($token),
                    'expired_at' => $this->newExpiry(),
                 ])
                ->fresh();

            return [ $verifyCode, $token ];
        });
    }

    /**
     * Attempts find the VerifyCode and then verify the token matches. Account
     * will be verified is matches, code wil be deleted regardless if found.
     *
     * @param  $code  string
     * @param  $token  string
     * @return ?VerifyCode
     */
    protected function attemptVerify(string $code, string $token) : ?VerifyCode
    {
        return DB::transaction(function () use ($code, $token) {
            $verifyCode = VerifyCode::query()
                ->where('expired_at', '>', now())
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if (is_null($verifyCode)) {
                return null;
            }

            // Whether or not the token is valid, the code is deleted.
            $verifyCode->delete();

            // Ensure the token the request has given matches the original
            if (!$this->hasher->check($token, $verifyCode->token)) {
                return null;
            }

            // Begin the process of verifying the account. A lock needs to be
            // taken on the account so we can ensure this request is the only
            // one verifying this account.
            $account = $verifyCode
                ->account()
                ->whereNull('verified_at')
                ->lockForUpdate()
                ->first();

            if (is_null($account)) {
                return null;
            }

            $account->verified_at = now();
            $account->save();

            $verifyCode->setRelation('account', $account);

            return $verifyCode;
        });
    }
}
