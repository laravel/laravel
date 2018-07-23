<?php

namespace App\Domain\Accounts\Verification\Events;

use App\Domain\Accounts\Account;

class AccountVerified
{
    /**
     * @var Account
     */
    public $account;

    /**
     * Creates a new AccountVerified event
     *
     * @param Accont  $account
     * @return void
     */
    public function __construct(Account $account)
    {
        $this->account = $account;
    }
}
